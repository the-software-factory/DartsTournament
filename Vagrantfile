VAGRANTFILE_API_VERSION = "2"

Vagrant.configure(VAGRANTFILE_API_VERSION) do |config|

    config.vm.box = "hashicorp/precise64"

    config.vm.provider "virtualbox" do |v|
        v.name = "Vendini Darts Tournament"
    end

    config.vm.network "forwarded_port", guest: 80, host: 8080   # Apache
    config.vm.network "forwarded_port", guest: 3306, host: 3310 # MySQL

    config.vm.synced_folder "./", "/var/www", create: true, group: "www-data", owner: "www-data"

    config.vm.provision "shell", path: ".provision/setup.sh"

end
