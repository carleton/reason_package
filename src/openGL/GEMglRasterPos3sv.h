 /* ------------------------------------------------------------------
  * GEM - Graphics Environment for Multimedia
  *
  *  Copyright (c) 2002 IOhannes m zmoelnig. forum::für::umläute. IEM
  *	zmoelnig@iem.kug.ac.at
  *  For information on usage and redistribution, and for a DISCLAIMER
  *  OF ALL WARRANTIES, see the file, "GEM.LICENSE.TERMS"
  *
  *  this file has been generated...
  * ------------------------------------------------------------------
  */

#ifndef INCLUDE_GEM_GLRASTERPOS3SV_H_
#define INCLUDE_GEM_GLRASTERPOS3SV_H_

#include "Base/GemGLBase.h"

/*
 CLASS
	GEMglRasterPos3sv
 KEYWORDS
	openGL	0
 DESCRIPTION
	wrapper for the openGL-function
	"glRasterPos3sv( GLshort* v)"
 */

class GEM_EXTERN GEMglRasterPos3sv : public GemGLBase
{
	CPPEXTERN_HEADER(GEMglRasterPos3sv, GemGLBase);

	public:
	  // Constructor
	  GEMglRasterPos3sv (t_float, t_float, t_float);	// CON
	protected:
	  // Destructor
	  virtual ~GEMglRasterPos3sv ();
	  // Do the rendering
	  virtual void	render (GemState *state);

	// variable
	GLshort	v[3];		// VAR
	virtual void	vMess(t_float, t_float, t_float);	// FUN

	private:

	// we need one inlet
	  t_inlet *m_inlet;

	// static member functions
         static void    vMessCallback (void*, t_floatarg, t_floatarg, t_floatarg);
};
#endif // for header file