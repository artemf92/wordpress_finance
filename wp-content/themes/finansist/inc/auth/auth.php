<? 
function wpb_login_logo() { ?>
  <style type="text/css">
    #registerform h2, #registerform .acf-user-register-fields, .language-switcher, #backtoblog {
      display: none !important;
    }
    body {
      /* background-color: #fff !important; */
    }
    .login form {
      background: #f0f0f1 !important;
      border: none !important;
      box-shadow: none !important;
    }
    #wp-submit {
      float: none;
      width: 100%;
      margin-top: 15px;
    }
    .login form {
      padding-top: 0 !important;
      margin-top: 0 !important;
    }
    .login h1 a {
      background: none !important;
      width: auto !important;
      text-indent: initial !important;
      height: initial !important;
      font-size: 22px !important;
      text-transform: uppercase;
    }
  </style>
  <?php }
  add_action( 'login_enqueue_scripts', 'wpb_login_logo' );
  
  function my_login_logo_url_title() {
    return __('Авторизация');
  }
  add_filter( 'login_headertext', 'my_login_logo_url_title' );
?>