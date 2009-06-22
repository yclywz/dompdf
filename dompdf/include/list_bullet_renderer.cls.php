<?php
/**
 * DOMPDF - PHP5 HTML to PDF renderer
 *
 * File: $RCSfile: list_bullet_renderer.cls.php,v $
 * Created on: 2004-06-23
 *
 * Copyright (c) 2004 - Benj Carson <benjcarson@digitaljunkies.ca>
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this library in the file LICENSE.LGPL; if not, write to the
 * Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA
 * 02111-1307 USA
 *
 * Alternatively, you may distribute this software under the terms of the
 * PHP License, version 3.0 or later.  A copy of this license should have
 * been distributed with this file in the file LICENSE.PHP .  If this is not
 * the case, you can obtain a copy at http://www.php.net/license/3_0.txt.
 *
 * The latest version of DOMPDF might be available at:
 * http://www.digitaljunkies.ca/dompdf
 *
 * @link http://www.digitaljunkies.ca/dompdf
 * @copyright 2004 Benj Carson
 * @author Benj Carson <benjcarson@digitaljunkies.ca>
 * @contributor Helmut Tischer <htischer@weihenstephan.org>
 * @package dompdf
 * @version 0.5.1
 *
 * Changes
 * @contributor Helmut Tischer <htischer@weihenstephan.org>
 * @version 20090622
 * - bullet size proportional to font size, center position
 */

/* $Id: list_bullet_renderer.cls.php,v 1.7 2006-10-26 17:07:23 benjcarson Exp $ */

/**
 * Renders list bullets
 *
 * @access private
 * @package dompdf
 */
/*
class Inline_Positioner extends Positioner {

  function __construct(Frame_Decorator $frame) { parent::__construct($frame); }

  //........................................................................

  function position() {
    $cb = $this->_frame->get_containing_block();

    // Find our nearest block level parent and access its lines property.
    $p = $this->_frame->find_block_parent();

    // Debugging code:

//     pre_r("\nPositioning:");
//     pre_r("Me: " . $this->_frame->get_node()->nodeName . " (" . (string)$this->_frame->get_node() . ")");
//     pre_r("Parent: " . $p->get_node()->nodeName . " (" . (string)$p->get_node() . ")");

    // End debugging

    if ( !$p )
      throw new DOMPDF_Exception("No block-level parent found.  Not good.");

    $line = $p->get_current_line();
    
    $this->_frame->set_position($cb["x"] + $line["w"], $line["y"]);

  }
}
*/
class List_Bullet_Renderer extends Abstract_Renderer {

  //........................................................................

  function render(Frame $frame) {

    $style = $frame->get_style();
    $font_size = $style->get_font_size();
    $line_height = $style->length_in_pt($style->line_height, $frame->get_containing_block("w"));

    // Handle list-style-image
    // If list style image is requested but missing, fall back to predefined types
    if ( $style->list_style_image != "none" &&
         strcmp($img = $frame->get_image_url(), DOMPDF_LIB_DIR . "/res/broken_image.png") != 0) {

      list($x,$y) = $frame->get_position();
      $w = $frame->get_width();
      $h = $frame->get_height();
      $x += $w/2; //Todo: Image seem not to be positioned top right, but top center. Why?
      $y -= ($line_height - $font_size)/2; //Reverse hinting of list_bullet_positioner

      $this->_canvas->image( $img, $frame->get_image_ext(), $x, $y, $w, $h);

    } else {

      $bullet_style = $style->list_style_type;

      $fill = false;

      switch ($bullet_style) {

      default:
      case "disc":
        $fill = true;

      case "circle":
        list($x,$y) = $frame->get_position();
        $r = ($font_size*(List_Bullet_Frame_Decorator::BULLET_SIZE /*-List_Bullet_Frame_Decorator::BULLET_THICKNESS*/ ))/2;
        $x -= $font_size*(List_Bullet_Frame_Decorator::BULLET_SIZE/2);
        $y += ($font_size*(1-List_Bullet_Frame_Decorator::BULLET_DESCENT))/2;
        $o = $font_size*List_Bullet_Frame_Decorator::BULLET_THICKNESS;
        $this->_canvas->circle($x, $y, $r, $style->color, $o, null, $fill);
        break;

      case "square":
        list($x, $y) = $frame->get_position();
        $w = $font_size*List_Bullet_Frame_Decorator::BULLET_SIZE;
        $x -= $w;
        $y += ($font_size*(1-List_Bullet_Frame_Decorator::BULLET_DESCENT-List_Bullet_Frame_Decorator::BULLET_SIZE))/2;
        $this->_canvas->filled_rectangle($x, $y, $w, $w, $style->color);
        break;

      }
    }
  }
}
?>