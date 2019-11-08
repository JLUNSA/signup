# 培训报名平台
## 安装

1. 将所有文件复制到web目录，按照如下规则配置服务器
#### Nginx
```
server {
        listen 80 default_server;
        root /var/www/signup;

        index index.html index.htm index.php index.nginx-debian.html;

        server_name _;

        location /static {
                try_files $uri /dev/null = 404;
        }

        location / {
                rewrite / /index.php break;
                include snippets/fastcgi-php.conf;
                fastcgi_pass unix:/var/run/php/php7.2-fpm.sock;
        }

        location ~ /\.ht {
                deny all;
        }
}
```
#### IIS
```
<?xml version="1.0" encoding="UTF-8"?>
<configuration>
    <system.webServer>
        <rewrite>
            <rules>
                <rule name="static" stopProcessing="true">
                    <match url="^static*" ignoreCase="false" />
                    <action type="None" />
                </rule>
                <rule name="index" stopProcessing="true">
                    <match url="^(.*)" />
                    <action type="Rewrite" url="/index.php/{R:1}" />
                </rule>
            </rules>
        </rewrite>
    </system.webServer>
</configuration>
```


#### APACHE
```
在网站根目录下新建.htaccess文件，写入如下内容:
RewriteEngine On

RewriteBase /  

RewriteCond %{REQUEST_FILENAME} !-f

RewriteCond %{REQUEST_FILENAME} !-d  

RewriteRule ^(.*)$ /index.php?/$1 [L] 
```

2. 导入`install.sql`


## TODO
