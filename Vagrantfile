
Vagrant.configure("2") do |config|
    config.vm.provider :libvirt do |libvirt|
        libvirt.cpus = 1
        libvirt.memory = 1024
    end

    config.vm.box = "generic/ubuntu1804"
    config.vm.hostname = "brave-forum-auth"

    config.vm.synced_folder "./", "/var/www/auth", type: "rsync",
        rsync__exclude: [".settings/", ".buildpath", ".project"]

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

        apt-get install -y git apache2 php-cli php-fpm php-curl php-mysql php-xsl composer

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
        a2enmod rewrite proxy_fcgi setenvif
        a2ensite 010-brave
        a2dissite 000-default
        a2enconf php7.2-fpm
        service apache2 reload

        chown vagrant:vagrant /var/www
    SHELL

    config.vm.provision "up", type: "shell", run: "always", privileged: false, inline: <<-SHELL
        PASSWD='vagrant'

        # install
        if [ ! -f phpBB-3.2.2.tar.bz2 ]; then
            wget https://www.phpbb.com/files/release/phpBB-3.2.2.tar.bz2
            tar jxf phpBB-3.2.2.tar.bz2
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
        echo "URLs (change IP as needed):"
        echo "Forum auth http://192.168.121.99"
        echo "phpMyAdmin http://192.168.121.99/phpmyadmin (root/$PASSWD)"
        echo "phpBB http://192.168.121.99/phpbb"
        echo "(install with Database ...: server: 127.0.0.1, username: root, password: $PASSWD, name: phpbb)"
        echo "SSH user: vagrant/$PASSWD"
        echo "mount: $ sshfs vagrant@192.168.121.99:/ /mnt/brave-forum-auth"
        echo "unmount: fusermount -u /mnt/brave-forum-auth"
        echo "-- ifconfig eth0 | grep inet :"
        /sbin/ifconfig eth0 | grep "inet "
        echo " "
    SHELL
end
