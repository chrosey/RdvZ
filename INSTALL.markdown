#  RdvZ 2.0.3

Before running the install script please read carefully the README file,
especially the 'Requirements' section.

When your web server setup is complete, take the time to prepare the
information you will be asked in the install script, like :
* database server address
* database user (with privileges !!!)
* database user password
* database name (no need to create the database itself, the script
   handles it

If you want to use a LDAP server :
* ldap server address
* ldap server port
* ldap base dn (the branch where the users are stored)
* some ldap fields name, prepare your LDAP attributes glossary !

If you want to use CAS authentication :
* cas server address
* cas server port


Then, when you are ready, launch the script and read carefully 
everything it displays :

        $ ./install or bash install


-------------------------------------------------------------------------------------


Then you need to set up a VirtualHost for your Apache server. Here are sample 
files, don't forget to change the pathes :

        [/etc/apache2/httpd.conf]
        <VirtualHost 127.0.0.1:80>
          ServerName rdvz.localhost
          DocumentRoot "/home/symfony/rdvz/web"
          DirectoryIndex index.php
          <Directory "/home/symfony/rdvz/web">
            AllowOverride All
            Allow from All
          </Directory>

          Alias /sf /home/symfony/rdvz/lib/vendor/symfony/data/web/sf
          <Directory "/home/symfony/rdvz/lib/vendor/symfony/data/web/sf">
            AllowOverride All
            Allow from All
          </Directory>
        </VirtualHost>

        [/etc/hosts]
        127.0.0.1 rdvz.localhost

Of course this sample configuration only works on your local machine, you need 
to adapt it in order to access your Apache server from the Internet.


-------------------------------------------------------------------------------------


The VirtualHost sample above only allows urls like rdvz.my-domain.com, but you
may want to access RdvZ as a module of your main domain, like www.my-domain.com/rdvz.
To do this you need to add these lines in your VirtualHost :

        [/etc/apache2/httpd.conf]
        <VirtualHost 127.0.0.1:80>
          # ....
          # ....
          Alias /rdvz /home/symfony/rdvz/web/
          <Directory "/home/symfony/rdvz/web">
             AllowOverride All
             Allow from All
          </Directory>
        </VirtualHost>

Then go in your .htaccess and replace this line :

        [/home/symfony/rdvz/web/.htaccess]
        RewriteRule ^(.*)$ index.php [QSA,L]

by this one : 

        [/home/symfony/rdvz/web/.htaccess]
        RewriteRule ^(.*)$ /rdvz/index.php [QSA,L]


-------------------------------------------------------------------------------------


Now you have to define the cache/ and log/ directories as writable for your
Apache server. The safest way is to switch the directory group to www-data,
and grant associated permissions :       

        $ chmod 775 cache/ log/
        $ chown -R :www-data cache/ log/


Finally you need to activate the mod_rewrite Apache module and restart
the Apache server. You may need root privileges to do this :

        $ a2enmod rewrite
        $ /etc/init.d/apache2 restart (or start if it is not started)


-------------------------------------------------------------------------------------


/!\ If you are using a LDAP server.
The install script presumes that you LDAP server allows anonymous reading access,
if it is not the fact, you have to provide the information necessary to a correct
binding.

Go to [apps/frontend/config/app.yml]

        all:
        ...
          ldap_server:
            host: ldap.your.host
            port: your_port
            basedn: dc=your_branch
            options: {<?php echo LDAP_OPT_PROTOCOL_VERSION ?>: 3}
            # # #
            # THE LINES YOU MUST ADD ARE BELOW
            # # #
            dn: your_access_granted_user
            password: the_password_of_your_user

*IF YOUR USER DOES NOT HAVE TO PROVIDE A PASSWORD TO BE AUTHENTICATED, DON'T WRITE
THIS LINE.* Otherwise RdvZ will try to authenticate your user with a blank password.
