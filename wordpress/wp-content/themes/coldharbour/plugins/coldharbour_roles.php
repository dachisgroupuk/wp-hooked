<?php
// if setting up roles or content types, it's imperative to use 'after_setup_theme' as the hook
// using 'init' is too late in the bootstrap process
add_action( 'after_setup_theme' , 'coldharbour_roles');

/**
 * coldharbour_roles
 *
 * Create the extra roles needed to let local bloggers add content
 * NOTE - this clears the role's capabilities on load, then redeclares the role
 * leaving us with a fresh user.
 *
 * @return void
 * @author Chris Adams
 **/
function coldharbour_roles()
{
  // until we hear otherwise, assume base capabilities of the contributor role 
  $contributor_role = get_role('contributor');
  // we need to remove the role beforehand to be sure we're working a clean sheet,
  // and to stop it failing silently when we try to add an already existing role
  $local_blogger = remove_role('local_blogger');
  $local_blogger = add_role('local_blogger', 'Local Blogger', $contributor_role->capabilities);
  $local_blogger = get_role('local_blogger');
  $local_blogger->add_cap('upload_files');

  $regional_editor = get_role('Editor'); 
  
  $regional_editor = remove_role('regional_editor');
  $regional_editor = add_role('regional_editor','Regional Editor', $regional_editors->capabilities);
  $regional_editor = get_role('regional_editor');
  /**
    * add_internal_content_type_cats
    *
    * Add editorial access to internal BFC content types
    * TODO Add error catching code with WP_Error
    * http://xref.yoast.com/trunk/nav.html?_functions/wp_error.html
    * http://codex.wordpress.org/Function_Reference/WP_Error
    *
   *
   * @return void
   * @author Chris Adams
   **/
  function add_internal_content_type_cats($role)
  {
    $updated_role = get_role($role);
    $updated_role->add_cap('edit_casestudy');
    
  }

  /**
   * add_editorial_powers
   *
   * Add powers to the more editorial roles, to let them create and publish content
   * @return void
   * @author Chris Adams
   **/
  function add_editorial_powers($role)
  {

    $updated_role = get_role($role);

    $updated_role->add_cap('publish_casestudy');
    $updated_role->add_cap('edit_published_casestudy');
    $updated_role->add_cap('edit_others_casestudy');
    $updated_role->add_cap('delete_casestudy');
    $updated_role->add_cap('delete_private_casestudy');
    $updated_role->add_cap('delete_published_casestudy');
    $updated_role->add_cap('delete_others_casestudy');
    $updated_role->add_cap('edit_private_casestudy');
    $updated_role->add_cap('edit_published_casestudy');
    
  }

  // now add the capabilities for roles
  add_internal_content_type_cats('editor');
  add_internal_content_type_cats('author');
  add_internal_content_type_cats('administrator');

  // give extra powers to trusted roles
  add_editorial_powers('editor');
  add_editorial_powers('administrator');

}

?>