<?php
/**
 * Fuel is a fast, lightweight, community driven PHP5 framework.
 *
 * @package    Fuel
 * @version    1.8
 * @author     Fuel Development Team
 * @license    MIT License
 * @copyright  2010 - 2016 Fuel Development Team
 * @link       http://fuelphp.com
 */

/**
 * The Welcome Controller.
 *
 * A basic controller example.  Has examples of how to set the
 * response body and status.
 *
 * @package  app
 * @extends  Controller
 */
class Controller_Mypage extends Controller_My
{
  public $template = "syokudo/template";
	/**
	 * The basic welcome message
	 *
	 * @access  public
	 * @return  Response
	 */
	public function action_index()
	{
    $user = new Model_User();
    $node = new Model_Node();
    
    $login_user = Session::get('user');
    
    if(!isset($login_user)) {
      $login_user['uid'] = 0;
    }
    
    $node_list = $node->get_node();
    foreach($node_list as $key => $value) {
      $good_user = $node->get_good_user_data($value['nid']);
      
      $node_list[$key]["good_num"] = count($good_user);
      if(count($good_user) >= 1) {
        $node_list[$key]["is_good"] = true;
      } else {
        $node_list[$key]["is_good"] = false;
      }
      $ungood_user = $node->get_ungood_user_data($value['nid']);
      
      $node_list[$key]["ungood_num"] = count($ungood_user);
      if(count($good_user) >= 1) {
        $node_list[$key]["is_ungood"] = true;
      } else {
        $node_list[$key]["is_ungood"] = false;
      }
      $favorite_node = $node->get_good_favorite_node($login_user['uid'], $value['nid']);
      if(count($favorite_node) >= 1) {
        $node_list[$key]["is_favorite"] = true;
      } else {
        $node_list[$key]["is_favorite"] = false;
      }
    }

    $comment_list = $node->get_node_comment_list();
    if($login_user['uid'] != 0) {
      $follow = $user->get_user_follow($login_user['uid']);
      $favorite_url = $user->get_user_favorite_url($login_user['uid']);
      // メインナビゲーションを取得
      $curl = Request::forge('http://syokudo.jpn.org/api/navigation/'.$login_user['uid'], 'curl');
      $response = $curl->execute()->response();
      $list = \Format::forge($response->body,'json')->to_array();
      $navigation = $list;
      Session::set('navigation', $navigation);
    } else {
      //Response::redirect('/index.php/login', 'refresh', 200);
      $follow = array();
      $favorite_url = array();
      $login_user['user_name'] = "AnonymousUser";
      $login_user['picture'] = 0;
      $login_user['user_body'] = '';
      $navigation = array();
      Session::set('navigation', $navigation);
    }
    
    // メインナビゲーションを取得
    $curl = Request::forge('http://syokudo.jpn.org/api/navigation/29', 'curl');
    $response = $curl->execute()->response();
    $list = \Format::forge($response->body,'json')->to_array();
        
    // サブナビゲーションを取得
    $curl = Request::forge('http://syokudo.jpn.org/api/user_sub_menu/29', 'curl');
    $response = $curl->execute()->response();
    $sub_menu_list = \Format::forge($response->body,'json')->to_array();
      
    $curl = Request::forge('http://syokudo.jpn.org/api/file', 'curl');
    $response = $curl->execute()->response();
    $this->image = \Format::forge($response->body,'json')->to_array();
    $this->image[0] = '/assets/img/anonymous_user.jpeg';
              
    $page = [];
    $current_link;
    foreach($list as $id => $data) {
      $list[$id]["menu_name"] = str_replace('HOME', 'Welcome', $list[$id]["menu_name"]);
      $link_name = strtolower($list[$id]["menu_name"]);
      $page[$link_name] = $data;
      $path1 = explode('/', $_SERVER['PHP_SELF']);
      if(isset($path1[2])) { 
        $path1 = $path1[2];
      } else {
        Response::redirect('/welcome');
      }
      if(str_replace('/index.php/', '', $path1) == $link_name) {
        $current_link = $link_name;
      }
    }
    $current_link = "welcome";
    if(!empty($current_link)) {
      if($page[$current_link]['type'] == 2) {
          $tid = $page[$current_link]['menu_id'];
          $curl = Request::forge('http://syokudo.jpn.org/api/page/'. $tid, 'curl');
          $response = $curl->execute()->response();
          $data = \Format::forge($response->body,'json')->to_array();
          $this->content_data = $data[0]["body_value"];
      }
    }
    
    $data = html_entity_decode($this->content_data);
    $this->template->content = View::forge('syokudo/welcome/index', [], false)->auto_filter(false);
    $this->template->content->set("data1", $data);

    $login_user['follow'] = $follow;
    $login_user['favorite_url'] = $favorite_url;
    $this->template->title = 'Example Page';
    $this->template->user = $login_user;
    //$this->template->user_navigation = $navigation;
    $this->template->content = View::forge('syokudo/welcome/index', ["data1" => $this->content_data], false)->auto_filter(false);
    return $this->template;
	}
  
  public function action_profile()
  {
    
          // メインナビゲーションを取得
        $curl = Request::forge('http://syokudo.jpn.org/api/navigation/29', 'curl');
        $response = $curl->execute()->response();
        $list = \Format::forge($response->body,'json')->to_array();
        
        // サブナビゲーションを取得
        $curl = Request::forge('http://syokudo.jpn.org/api/user_sub_menu/29', 'curl');
        $response = $curl->execute()->response();
        $sub_menu_list = \Format::forge($response->body,'json')->to_array();
      
        $curl = Request::forge('http://syokudo.jpn.org/api/file', 'curl');
        $response = $curl->execute()->response();
        $this->image = \Format::forge($response->body,'json')->to_array();
        $this->image[0] = '/assets/img/anonymous_user.jpeg';
      
      
      

        
        $page = [];
        $current_link;
        foreach($list as $id => $data) {
          $list[$id]["menu_name"] = str_replace('HOME', 'Welcome', $list[$id]["menu_name"]);
          $link_name = strtolower($list[$id]["menu_name"]);
          $page[$link_name] = $data;
          $path1 = explode('/', $_SERVER['PHP_SELF']);
          if(isset($path1[2])) { 
            $path1 = $path1[2];
          } else {
            Response::redirect('/welcome');
          }
          if(str_replace('/index.php/', '', $path1) == $link_name) {
            $current_link = $link_name;
          }
        }
    
        $current_link = "profile";
        if(!empty($current_link)) {
          if($page[$current_link]['type'] == 2) {
              $tid = $page[$current_link]['menu_id'];
              $curl = Request::forge('http://syokudo.jpn.org/api/page/'. $tid, 'curl');
            
              $response = $curl->execute()->response();
              $data = \Format::forge($response->body,'json')->to_array();
              $this->content_data = $data[0]["body_value"];
            
            
            
          }
        }
    
        $data = html_entity_decode ($this->content_data);
        $this->template->content = View::forge('syokudo/welcome/index', [], false)->auto_filter(false);
        $this->template->content->set("data1", $data);
        return $this->template;
  }
  
  public function action_menu()
  {
      // Request_Curl オブジェクトを作成
      $curl = Request::forge('http://syokudo.jpn.org/api/node_list/taxonomy/5', 'curl');

      // リクエストを実行
      $response = $curl->execute()->response();
      $list = \Format::forge($response->body,'json')->to_array();
      foreach($list as $id => $data) {
        $list[$id]["body_value"] = strip_tags(str_replace(PHP_EOL, '', mb_substr($data["body_value"], 0, 100)));
      }
      $this->template->content = View::forge('syokudo/menu/index', ["list" => $list], false);
  }
  
  public function action_detail() {
    if(Input::get('nid')) {
        // Request_Curl オブジェクトを作成
        $curl = Request::forge('http://syokudo.jpn.org/api/node_data/'.Input::get('nid'), 'curl');
        // リクエストを実行
        $response = $curl->execute()->response();
        $data = \Format::forge($response->body,'json')->to_array();
        $data[0]["body_value"] = str_replace('/sites', 'http://syokudo.jpn.org/sites', $data[0]["body_value"]);
        $this->template->image = (isset($data[0]["image"][0])) ? $data[0]["image"][0] : ""; 
        $this->template->title = $data[0]["title"];
        $this->template->content = View::forge('syokudo/blog/detail', ["data" => $data[0], "title" => $this->template->title, "image" => $this->image], false);
    } else {
      return Response::forge(Presenter::forge('welcome/404'), 404);
    }
  }
  
  public function action_blog()
  {
        // Request_Curl オブジェクトを作成
        $curl = Request::forge('http://syokudo.jpn.org/api/node_list/taxonomy/7', 'curl');

        // リクエストを実行
        $response = $curl->execute()->response();
        $list = \Format::forge($response->body,'json')->to_array();
        foreach($list as $id => $data) {
          $list[$id]["body_value"] = strip_tags(str_replace(PHP_EOL, '', mb_substr($data["body_value"], 0, 100)));
        }
        $this->template->content = View::forge('syokudo/menu/index', ["list" => $list], false);
  }
  
  public function action_map() 
  {
    
          // メインナビゲーションを取得
        $curl = Request::forge('http://syokudo.jpn.org/api/navigation/29', 'curl');
        $response = $curl->execute()->response();
        $list = \Format::forge($response->body,'json')->to_array();
        
        // サブナビゲーションを取得
        $curl = Request::forge('http://syokudo.jpn.org/api/user_sub_menu/29', 'curl');
        $response = $curl->execute()->response();
        $sub_menu_list = \Format::forge($response->body,'json')->to_array();
      
        $curl = Request::forge('http://syokudo.jpn.org/api/file', 'curl');
        $response = $curl->execute()->response();
        $this->image = \Format::forge($response->body,'json')->to_array();
        $this->image[0] = '/assets/img/anonymous_user.jpeg';
      
      
      

        
        $page = [];
        $current_link;
        foreach($list as $id => $data) {
          $list[$id]["menu_name"] = str_replace('HOME', 'Welcome', $list[$id]["menu_name"]);
          $link_name = strtolower($list[$id]["menu_name"]);
          $page[$link_name] = $data;
          $path1 = explode('/', $_SERVER['PHP_SELF']);
          if(isset($path1[2])) { 
            $path1 = $path1[2];
          } else {
            Response::redirect('/welcome');
          }
          if(str_replace('/index.php/', '', $path1) == $link_name) {
            $current_link = $link_name;
          }
        }
    
        $current_link = "map";
        if(!empty($current_link)) {
          if($page[$current_link]['type'] == 2) {
              $tid = $page[$current_link]['menu_id'];
              $curl = Request::forge('http://syokudo.jpn.org/api/page/'. $tid, 'curl');
            
              $response = $curl->execute()->response();
              $data = \Format::forge($response->body,'json')->to_array();
              $this->content_data = $data[0]["body_value"];
            
            
            
          }
        }    
        $data = html_entity_decode ($this->content_data);
        $this->template->title = 'Example Page';
        $this->template->content = View::forge('syokudo/welcome/index', [], false)->auto_filter(false);
        $this->template->content->set("data1", $data);
        $this->template->sub_menu = $this->sub_menu;
        return $this->template;
  }
  
  public function action_contact()
  {
      // POSTの場合
      if(Input::method() == 'POST') {
        $name = Input::post('name');
        $contact = Input::post('contact');
        $type  = Input::post('type');
        $body  = Input::post('body');
        
        // Request_Curl オブジェクトを作成
        $curl = Request::forge('http://syokudo.jpn.org/api/mail', 'curl');
        $curl->set_method('get');
        $curl->set_params(array('uid' => 29, 'name' => $name, 'contact' => $contact, 'type' => $type, 'body' => $body));
        $response = $curl->execute()->response();
        
      }
      $data = array();
      $this->template->content = View::forge('syokudo/contact/index', $data);
  }

	/**
	 * A typical "Hello, Bob!" type example.  This uses a Presenter to
	 * show how to use them.
	 *
	 * @access  public
	 * @return  Response
	 */
	public function action_hello()
	{
		return Response::forge(Presenter::forge('welcome/hello'));
	}

	/**
	 * The 404 action for the application.
	 *
	 * @access  public
	 * @return  Response
	 */
	public function action_404()
	{
		return Response::forge(Presenter::forge('welcome/404'), 404);
	}
}
