name "wp-hooked_prod_server"
description "WP Hooked user group - production environment"
run_list(
  "recipe[openssl]",
  "recipe[php::php53]",
  "recipe[php::module_mysql]",
  "recipe[users::deploy_user]",
  "recipe[memcached]",
  "recipe[php::module_mcrypt]",
  "recipe[php::module_memcache]",
  "recipe[php::module_apc]",
  "recipe[apache2]",
  "recipe[apache2::mod_rewrite]",
  "recipe[apache2::mod_expires]",
  "recipe[apache2::mod_fcgid]",
  "recipe[wp_hooked]"
)
override_attributes(
  "apache" => {
    :mpm => "worker",
    :fcgid => {
      :max_process_count => 50,
      :ipc_connect_timeout => 600,
      :ipc_comm_timeout => 600
    }
  },
  :php => {
    :error_reporting => "E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR"
  },
  :wp_hooked => {
    :web_server_name => "wp-hooked.com",
    :web_server_aliases => [ "www.wp-hooked.com" ],
    :deploy_branch => "production",
    :db_user => "wp_hooked",
    :db_password => "av3L34+epW",
    :db_host => "mysql-prod2.headshift.com",
    :db_name => "wp_hooked_prod0"
  }
)
