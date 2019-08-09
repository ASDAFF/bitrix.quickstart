<?php
/**
 *  KDAPHPExcel
 *
 *  Copyright (c) 2006 - 2013 KDAPHPExcel
 *
 *  This library is free software; you can redistribute it and/or
 *  modify it under the terms of the GNU Lesser General Public
 *  License as published by the Free Software Foundation; either
 *  version 2.1 of the License, or (at your option) any later version.
 *
 *  This library is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 *  Lesser General Public License for more details.
 *
 *  You should have received a copy of the GNU Lesser General Public
 *  License along with this library; if not, write to the Free Software
 *  Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 *
 *  @category   KDAPHPExcel
 *  @package    KDAPHPExcel_Writer
 *  @copyright  Copyright (c) 2006 - 2013 KDAPHPExcel (http://www.codeplex.com/KDAPHPExcel)
 *  @license    http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt    LGPL
 *  @version    1.7.9, 2013-06-02
 */


/**
 *  KDAPHPExcel_Writer_IWriter
 *
 *  @category   KDAPHPExcel
 *  @package    KDAPHPExcel_Writer
 *  @copyright  Copyright (c) 2006 - 2013 KDAPHPExcel (http://www.codeplex.com/KDAPHPExcel)
 */
interface KDAPHPExcel_Writer_IWriter
{
    /**
     *  Save KDAPHPExcel to file
     *
     *  @param   string       $pFilename  Name of the file to save
     *  @throws  KDAPHPExcel_Writer_Exception
     */
    public function save($pFilename = NULL);

}
