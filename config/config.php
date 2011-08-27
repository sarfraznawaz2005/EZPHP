; settings for EZPHP

[application]
default_controller = "home"
timezone = "Europe/London"


[security]
error_reporting = E_ALL
display_errors = 1
xss_filter = 0


[database]
use_db = 1
db_class = "db_mysql"
db_name = "ezphpdb"
db_hostname = "localhost"
db_username = "root"
db_password = ""
db_port = 


[template]
use_template = 1
template_dir = "default"


[cache]
enable_cache = 0
cache_lifetime = "3600"


[url]
url_suffix = ".html"
