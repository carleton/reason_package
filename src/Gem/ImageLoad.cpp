////////////////////////////////////////////////////////
//
// GEM - Graphics Environment for Multimedia
//
// zmoelnig@iem.kug.ac.at
//
// Implementation file
//
//    Copyright (c) 1997-1999 Mark Danks.
//    Copyright (c) Günther Geiger.
//    Copyright (c) 2001-2002 IOhannes m zmoelnig. forum::für::umläute
//    Copyright (c) 2002-2003 james tittle/tigital
//
//    For information on usage and redistribution, and for a DISCLAIMER OF ALL
//    WARRANTIES, see the file, "GEM.LICENSE.TERMS" in this distribution.
//
/////////////////////////////////////////////////////////
#include "ImageIO.h"
#include "Gem/RTE.h"
#include "Gem/SynchedWorkerThread.h"

#include "plugins/imageloader.h"
namespace gem {
  class PixImageLoader : public gem::plugins::imageloader {
  private:
    static PixImageLoader*s_instance;
    std::vector<gem::plugins::imageloader*>m_loaders;
    std::vector<std::string>m_ids;
    bool m_canThread;

    PixImageLoader(void) : m_canThread(true) {
      gem::PluginFactory<gem::plugins::imageloader>::loadPlugins("image");
      std::vector<std::string>available_ids=gem::PluginFactory<gem::plugins::imageloader>::getIDs();

      addLoader(available_ids, "magick");
      addLoader(available_ids);

      if(m_ids.size()>0) {
        startpost("Image loading support:");
        unsigned int i;
        for(i=0; i<m_ids.size(); i++) {
          startpost(" %s", m_ids[i].c_str());
        }
        endpost();
      }


      m_canThread=true;
      unsigned int i;
      for(i=0; i<m_loaders.size(); i++) {
        if(!m_loaders[i]->isThreadable()) {
          m_canThread=false;
          break;
        }
      }
    }
    bool addLoader( std::vector<std::string>available, std::string ID=std::string(""))
    {
      int count=0;

      std::vector<std::string>id;
      if(!ID.empty()) {
        // if requested 'cid' is in 'available' add it to the list of 'id's
        if(std::find(available.begin(), available.end(), ID)!=available.end()) {
          id.push_back(ID);
        } else {
          // request for an unavailable ID
          verbose(2, "backend '%s' unavailable", ID.c_str());
          return false;
        }
      } else {
        // no 'ID' given: add all available IDs
        id=available;
      }

      unsigned int i=0;
      for(i=0; i<id.size(); i++) {
        std::string key=id[i];
        verbose(2, "trying to add '%s' as backend", key.c_str());
        if(std::find(m_ids.begin(), m_ids.end(), key)==m_ids.end()) {
          // not yet added, do so now!
          gem::plugins::imageloader*loader=
            gem::PluginFactory<gem::plugins::imageloader>::getInstance(key); 
          if(NULL==loader)break;
          m_ids.push_back(key);
          m_loaders.push_back(loader);
          count++;
          verbose(2, "added backend#%d '%s' @ 0x%x", m_loaders.size()-1, key.c_str(), loader);
        }
      }
      return (count>0);
    }

  public:
    virtual ~PixImageLoader(void) {
      unsigned int i;
      for(i=0; i<m_loaders.size(); i++) {
        delete m_loaders[i];
        m_loaders[i]=NULL;
      }
    }

    virtual bool load(std::string filename, imageStruct&result, gem::Properties&props) {
      unsigned int i;
      for(i=0; i<m_loaders.size(); i++) {
        if(m_loaders[i]->load(filename, result, props))
          return true;
      }
      return false;
    }

    static PixImageLoader*getInstance(void) {
      if(NULL==s_instance) {
        s_instance=new PixImageLoader();
      }
      return s_instance;
    }
    virtual bool isThreadable(void) {
      return m_canThread;
    }
  }; };
gem::PixImageLoader*gem::PixImageLoader::s_instance=NULL;


namespace gem { namespace image {
  struct PixImageThreadLoader : public gem::thread::SynchedWorkerThread {
    struct InData {
      load::callback cb;
      void*userdata;
      std::string filename;
      InData(load::callback cb_, void*data_, std::string fname) :
        cb(cb_),
        userdata(data_),
        filename(fname) {
      };
    };

    struct OutData {
      load::callback cb;
      void*userdata;
      imageStruct*img;
      gem::Properties props;
      OutData(const InData&in) :
        cb(in.cb),
        userdata(in.userdata),
        img(NULL) { 
      };
    };

    gem::PixImageLoader*imageloader;
    PixImageThreadLoader(void) :
      SynchedWorkerThread(false),
      imageloader(gem::PixImageLoader::getInstance())
    { 
      if(!imageloader->isThreadable()) 
        throw(42);
      start();
    }
    virtual ~PixImageThreadLoader(void) {
    }

    virtual void* process(id_t ID, void*data) {
      InData*in=reinterpret_cast<InData*>(data);
      OutData*out=new OutData(*in);
      
      // DOIT
      out->img=new imageStruct;
      if(!imageloader->load(in->filename, *out->img, out->props)) {
        delete out->img;
        out->img=0;
      }
      void*result=reinterpret_cast<void*>(out);
      //post("processing[%d] %p -> %p", ID, data, result);
      return result;
    };

    virtual void done(id_t ID, void*data) {
      OutData*out=reinterpret_cast<OutData*>(data);
      if(out) {
        (*(out->cb))(out->userdata, ID, out->img, out->props);
        delete out;
      } else {
        error("loaded image:%d with no data!", ID);
      }
    };

    virtual bool queue(id_t&ID, load::callback cb, void*userdata, std::string filename) {
      InData *in = new InData(cb, userdata, filename);
      return SynchedWorkerThread::queue(ID, reinterpret_cast<void*>(in));
    };

    static PixImageThreadLoader*getInstance(bool retry=true) {
      static bool didit=false;
      if(!retry && didit)
	return s_instance;
      didit=true;

      if(NULL==s_instance) {
        try {
          s_instance=new PixImageThreadLoader();
        } catch(int i) {
          static bool dunnit=false;
          if(!dunnit) {
            verbose(1, "threaded ImageLoading not supported!");
          }
          dunnit=true;
        }

        if(s_instance)
          s_instance->setPolling(true);
      }

      return s_instance;
    };

  private:
    static PixImageThreadLoader*s_instance;
  };
  PixImageThreadLoader*PixImageThreadLoader::s_instance=NULL;



  const load::id_t load::IMMEDIATE= 0;
  const load::id_t load::INVALID  =~0;

  bool load::sync(const std::string filename,
                  imageStruct&result,
                  gem::Properties&props) {
    gem::PixImageLoader*piximageloader=gem::PixImageLoader::getInstance();
    if(piximageloader) {
      if(piximageloader->load(filename, result, props)) {
        return true;
      }
    }
    return false;
  }
  
  bool load::async(load::callback cb,
                   void*userdata,
                   const std::string filename,
                   id_t&ID) {
    if(NULL==cb) {
      ID=INVALID;
      return false;
    }

    PixImageThreadLoader*threadloader=PixImageThreadLoader::getInstance();

    //post("threadloader %p", threadloader);
    
    if(threadloader) {
      return threadloader->queue(ID, cb, userdata, filename);
    }
    return sync(cb, userdata, filename, ID);
  }

  bool load::sync(load::callback cb,
                  void*userdata,
                  const std::string filename,
                  id_t&ID) {
    if(NULL==cb) {
      ID=INVALID;
      return false;
    }
    imageStruct*result=new imageStruct;
    gem::Properties props;
    if(sync(filename, *result, props)) {
      ID=IMMEDIATE;
      (*cb)(userdata, ID, result, props);
      return true;
    }
    ID=INVALID;
    return false;
  }

  bool load::cancel(id_t ID) {
    PixImageThreadLoader*threadloader=PixImageThreadLoader::getInstance(false);
    if(threadloader) {
      return threadloader->cancel(ID);
    }
    return false;
  }

  bool load::setPolling(bool value) {
    PixImageThreadLoader*threadloader=PixImageThreadLoader::getInstance();
    if(threadloader) {
      return threadloader->setPolling(value);
    }
    return true;
  }
  void load::poll(void) {
    PixImageThreadLoader*threadloader=PixImageThreadLoader::getInstance(false);
    if(threadloader) {
      threadloader->dequeue();
    }
  }


}; // image
}; // gem


/***************************************************************************
 *
 * image2mem - Read in an image in various file formats
 *
 ***************************************************************************/
GEM_EXTERN imageStruct *image2mem(const char *filename)
{
  gem::Properties props;
  imageStruct *img = new imageStruct();
  if(gem::image::load::sync(filename, *img, props))
    return img;
  
  delete img;
  return NULL;
}