
Vagrant.configure("2") do |config|
    config.vm.box = "generic/debian8"

    config.vm.network "forwarded_port", guest: 80, host: 8080, host_ip: "127.0.0.1"
    config.vm.network :private_network, type: "dhcp"

    config.vm.synced_folder "./", "/var/www/auth", type: "rsync"

    config.ssh.username = 'vagrant'
    config.ssh.password = 'vagrant'

    config.vm.provision "shell", inline: <<-SHELL
        PASSWD='vagrant'

        export DEBIAN_FRONTEND=noninteractive
        echo "LC_ALL=en_US.UTF-8" > /etc/environment

        apt-get update

        debconf-set-selections <<< "mysql-server mysql-server/root_password password $PASSWD"
        debconf-set-selections <<< "mysql-server mysql-server/root_password_again password $PASSWD"
        apt-get install -y mysql-server
        mysql -e 'CREATE DATABASE IF NOT EXISTS phpbb' -p$PASSWD
        mysql -e 'CREATE DATABASE IF NOT EXISTS forum_auth' -p$PASSWD

        apt-get install -y git apache2 php5 libapache2-mod-php5 php5-curl php5-mysql php5-xsl

        debconf-set-selections <<< "phpmyadmin phpmyadmin/dbconfig-install boolean true"
        debconf-set-selections <<< "phpmyadmin phpmyadmin/app-password-confirm password $PASSWD"
        debconf-set-selections <<< "phpmyadmin phpmyadmin/mysql/admin-pass password $PASSWD"
        debconf-set-selections <<< "phpmyadmin phpmyadmin/mysql/app-pass password $PASSWD"
        debconf-set-selections <<< "phpmyadmin phpmyadmin/reconfigure-webserver multiselect apache2"
        apt-get install -y phpmyadmin

        echo "Alias /phpbb /var/www/phpbb"              > /etc/apache2/sites-available/010-brave.conf
        echo "<VirtualHost *:80>"                      >> /etc/apache2/sites-available/010-brave.conf
        echo "    DocumentRoot /var/www/auth/web"      >> /etc/apache2/sites-available/010-brave.conf
        echo "    <Directory /var/www/auth/web/>"      >> /etc/apache2/sites-available/010-brave.conf
        echo "        AllowOverride All"               >> /etc/apache2/sites-available/010-brave.conf
        echo "    </Directory>"                        >> /etc/apache2/sites-available/010-brave.conf
        echo "</VirtualHost>"                          >> /etc/apache2/sites-available/010-brave.conf
        a2enmod rewrite
        a2ensite 010-brave
        a2dissite 000-default
        service apache2 reload

        chown vagrant:vagrant /var/www

        php -r "copy('https://getcomposer.org/installer', '/tmp/composer-setup.php');"
        php /tmp/composer-setup.php --install-dir=/usr/local/bin --filename=composer
    SHELL

    config.vm.provision "up", type: "shell", run: "always", privileged: false, inline: <<-SHELL
        PASSWD='vagrant'

        # install
        if [ ! -f phpBB-3.1.12.tar.bz2 ]; then
            wget https://www.phpbb.com/files/release/phpBB-3.1.12.tar.bz2
            tar jxf phpBB-3.1.12.tar.bz2
            mv phpBB3 /var/www/phpbb
            chmod 0666 /var/www/phpbb/config.php
            chmod 0777 /var/www/phpbb/store
            chmod 0777 /var/www/phpbb/cache
            chmod 0777 /var/www/phpbb/files
            chmod 0777 /var/www/phpbb/images/avatars/upload
        fi

        cd /var/www/auth
        if [ ! -f config/config.php ]; then
            cp config/config.dist.php config/config.php
        fi
        chmod 0777 logs
        composer install

        echo " "
        echo "Forum auth http://localhost:8080"
        echo "phpMyAdmin http://localhost:8080/phpmyadmin (root/$PASSWD)"
        echo "phpBB http://localhost:8080/phpbb (install with: DB server: 127.0.0.1, DB name: phpbb, DB username: root, DB password: $PASSWD)"
        echo "ifconfig:"
        /sbin/ifconfig eth0 | grep "inet addr"
        echo "SSH user: vagrant/$PASSWD"
        echo "mount on host, e. g.: $ sshfs vagrant@192.168.121.175:/ /mnt/brave-forum-auth"
        echo " "
    SHELL
end
