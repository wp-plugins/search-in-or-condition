<?php

/*
Plugin Name: Search in or condition
Plugin URI: http://www.solamentegratis.it
Description: This is the right plugin if you want to search every single word in OR condition
Version: 1.0
Author: <a href="http://www.tequilabumbum.it">Stefano Ferri</a>
Author URI: http://www.tequilabumbum.it
*/
if (!class_exists("sgSearchInORcondition")) {
 class sgSearchInORcondition {
    var $adminOptionsName = "sgSearchInORconditionAdminOptions";

 
    //Returns an array of admin options
    function getAdminOptions() {      
        //opzioni di default sul db
       $devloungeAdminOptions = array('min_lenght' => '4', 'search_type' => '2','splitter' => ' ','exclude' => 'from,where,when');
 
     $devOptions = get_option($this->adminOptionsName);
     
     if (!empty($devOptions)) {
      foreach ($devOptions as $key => $option)
        $devloungeAdminOptions[$key] = $option;
       }
       update_option($this->adminOptionsName, $devloungeAdminOptions);
      return $devloungeAdminOptions;
    }
    
    function init() {
      $this->getAdminOptions();
    } 
    
    
  function posts_where( $where ) {
    //stringaricerca
    $stringaricerca="";
  	if( is_search() ) {      
  	   if (isset($_GET['s'])){  
         //default value
  		  $conta=0;
  		  $min_lenght="4";
  		  $search_type="2";
  		  $splitter=" ";
  		  $exclude="from,where,when";
           
  	     //retrieve the admin storage option
        $devOptions = $this->getAdminOptions();
        
        if (is_int($devOptions['min_lenght'])) { 
          $min_lenght=$devOptions['min_lenght'];
        }
        if (is_int( $devOptions['search_type'])) { 
          $search_type=$devOptions['search_type'];
        }
        if ($devOptions['splitter']!="") { 
          $splitter=$devOptions['splitter'];
        }
        if ($devOptions['exclude']!="") { 
          $exclude=$devOptions['exclude'];
        }
 
 
  		  $strrequest=explode($splitter, $_GET['s']) ;
  		  $where="AND ( ";
  
         
        
  		  foreach ($strrequest as $element) {  
          $conta++;
 
  		    if (strlen($element)>= intval($min_lenght)){
            if (strpos($exclude.",",$element.",")=== false){
    		      //echo($element."-".strlen($element));
              if ($conta==1){
                if ($search_type==1){
                  $where.=" ((wp_posts.post_title LIKE '".$element."') OR (wp_posts.post_content LIKE '".$element."'))";
                }
                if ($search_type==2){
                  $where.=" ((wp_posts.post_title LIKE '% ".$element." %') OR (wp_posts.post_content LIKE '% ".$element." %') OR (wp_posts.post_title LIKE '%".$element." %') OR (wp_posts.post_content LIKE '%".$element." %') OR  (wp_posts.post_title LIKE '%".$element." %') OR (wp_posts.post_content LIKE '%".$element." %'))";
                }
                if ($search_type==3){
                  $where.=" ((wp_posts.post_title LIKE '%".$element."%') OR (wp_posts.post_content LIKE '%".$element."%'))";
                }
                
              }else{
                if ($search_type==1){
                  $where.=" OR ((wp_posts.post_title LIKE '".$element."') OR (wp_posts.post_content LIKE '".$element."')) ";
                }
                if ($search_type==2){
                  $where.=" OR ((wp_posts.post_title LIKE '% ".$element." %') OR (wp_posts.post_content LIKE '% ".$element." %') OR (wp_posts.post_title LIKE '%".$element." %') OR (wp_posts.post_content LIKE '%".$element." %') OR (wp_posts.post_title LIKE '%".$element." %') OR (wp_posts.post_content LIKE '%".$element." %')) ";
                }
                if ($search_type==3){
                  $where.=" OR ((wp_posts.post_title LIKE '%".$element."%') OR (wp_posts.post_content LIKE '%".$element."%')) ";
                }
              }
            } 
          }  
        }
        if ($conta==0){
          //set a default condition to satisfy and condition
          $where.=" 1=1 ";
        }
        
        $where.=") AND wp_posts.post_type IN ('post', 'page', 'attachment') AND (wp_posts.post_status = 'publish' OR wp_posts.post_author = 1 AND wp_posts.post_status = 'private') ";
      
  		}     
  	}
  	 
  	
  	return $where;
  }
    
    
    
    //parte admin
 //Prints out the admin page
 function printAdminPage() {
  
  $devOptions = $this->getAdminOptions();

  if (isset($_POST['update_SearchInORconditionSettings'])) {
    if (isset($_POST['SearchInORcondition_minlenght'])) {
      $devOptions['min_lenght'] = $_POST['SearchInORcondition_minlenght'];
    }
    if (isset($_POST['SearchInORcondition_searchtype'])) {
      $devOptions['search_type'] = $_POST['SearchInORcondition_searchtype'];
    }  
    if (isset($_POST['SearchInORcondition_splitter'])) {
      $devOptions['splitter'] = $_POST['SearchInORcondition_splitter'];
    }  
    if (isset($_POST['SearchInORcondition_exclude'])) {
      $devOptions['exclude'] = $_POST['SearchInORcondition_exclude'];
    }  
    
    
    update_option($this->adminOptionsName, $devOptions);    
    
?>
  <div class="updated"><p><strong><?php _e("Settings Updated.", "SearchInORcondition");?></strong></p></div>
<?php }?>
<p style="margin-left:0px;" id="ll"><a href="http://www.solamentegratis.it/laboratorio" target="_blank"><em>sgSearchInORcondition</em> by Solamente Gratis Lab</a></p>  
  <div class="wrap">
  <form method="post" action="<?php echo $_SERVER["REQUEST_URI"]; ?>">
    <h2>SolamenteGratis Plugin Series</h2>
    
    <h3>Set the min lenght to be relevart for query condition (>=)</h3>
    <input type="text" name="SearchInORcondition_minlenght" value="<?php echo $devOptions['min_lenght'] ?>">
    
    <h3>Set the "word search"  condition</h3>
    <p>Look to examples to understand clearly with the world "<strong>come</strong>".</p>
    <ul>
      <li>Example 1: "comet" -> <b>not found</b>; " come" --> <b>found</b>; " tcome " --> <b>not found</b>; " tcomet " --> <b>not found</b></li>
      <li>Example 2: "comet" -> <b>found</b>; " come" --> <b>found</b>; " tcome " --> <b>found</b>; " tcomet " --> <b>not found</b></li>
      <li>Example 3: "comet" -> <b>found</b>; " come" --> <b>found</b>; " tcome " --> <b>found</b>; " tcomet " --> <b>found</b></li>
    <ul>
      <select name="SearchInORcondition_searchtype">
        <option value="1" <?php selected("1", $devOptions['search_type'] ); ?>>Search only for the exact word (example 1)</option>
        <option value="2" <?php selected("2", $devOptions['search_type'] ); ?>>Search for the exact word or where it start or end with it (example 2)</option>
        <option value="3" <?php selected("3", $devOptions['search_type'] ); ?>>Search for everything contains the word in any position (example 3)</option>
      </select>
     <h3>Set the value to use to split the querystring (deafult=" " blank)</h3>
      <input type="text" name="SearchInORcondition_splitter" value="<?php echo $devOptions['splitter'];?>" size="5">
     <h3>Exclude this words from search (comma separator)</h3>
     <input type="text" name="SearchInORcondition_exclude" value="<?php echo $devOptions['exclude'];?>" size="200">

  <div class="submit"><input type="submit" name="update_SearchInORconditionSettings" value="<?php _e('Update Settings', 'SearchInORcondition') ?>" /></div>
  </form>
  </div>
  
  <p style="margin-left:0px;" id="ll"><a href="http://www.solamentegratis.it/laboratorio" target="_blank"><em>sgSearchInORcondition</em> by Solamente Gratis Lab</a></p>
  <?php
  }
  
   

  } //End Class SearchInORcondition
}

if (class_exists("sgSearchInORcondition")) {
  $dl_SearchInORcondition = new sgSearchInORcondition();
}


//Actions and Filters
if (isset($dl_SearchInORcondition)) {
  //Actions
   add_action('activate_wp-search-inorcondition/searchinorcondition.php',array(&$dl_SearchInORcondition, 'init'));
   
  //Filters
  add_filter('posts_where', array(&$dl_SearchInORcondition, 'posts_where'));
  
}

//Initialize the admin panel
if (!function_exists("SearchInORcondition_ap")) {

  function SearchInORcondition_ap() {
    global $dl_SearchInORcondition;
    if (!isset($dl_SearchInORcondition)) {
      
      return;
    }
    
    if (function_exists('add_options_page')) {
      add_options_page('sgInORcondition', 'sgInORcondition', 10,basename(__FILE__), array(&$dl_SearchInORcondition, 'printAdminPage'));
    }
  }
  
}

//crea il nuovo pannello sul menu
add_action('admin_menu', 'SearchInORcondition_ap');
?>