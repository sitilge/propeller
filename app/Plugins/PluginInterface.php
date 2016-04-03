<?php

namespace App\Plugins;

use App\Models\BusinessModel;
use Abimo\Config;
use Abimo\Request;
use Abimo\Template;

/** to create a new plugin:
 * create a new class [Pluginname]Plugin , which implements PluginInterface. 
 *    The first letter (only) of Pluginname should be capitalized.
 * in your Admin/[table].json, set columns > [columnname] > plugin to the value [Pluginname], 
 *    in all lower case. 
 */

interface PluginInterface {
  
  function __construct( BusinessModel $businessmodel, Config $config ) ;

  /** handles HTTP post updates for columns of this particular plugin type. 
   * @param string $columnname of the column we're receiving update post for. 
   *    (the table name is available from $businessmodel, injected in the constructor)
   * @param Request $request the Request, from whence you should be able to draw $_POST or other data
   *    (if not, just cheat and use the superglobals themselves)
   * @returns void - the caller can just use the passed Template they already have. 
   */ 
  public function postHandler( $columnname, Request $request ) ;
  
  /** configures a passed Template object to render the column on a row page
   * @param string $columnname of the column we're requesting a template object config for
   *    (the table name is available from $businessmodel, injected in the constructor)
   * @param Template $template the Template object that requires configuration. 
   * @returns void - the caller can just use the passed Template they already have. 
   */
  public function viewTemplate( $columnname, Template $template ) ;
  
}
