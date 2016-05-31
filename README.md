**Parser** for music lower price
=============================
![Built with PHP5](http://pxd.me/dompdf/www/images/php5-power-micro.png)

**Is an PHP & Angular application that parse the lowest price from Amazon & eBay.**

![](http://new.tinygrab.com/7020c0e8b02d88be877c02ce067a258785148ababa.png)

### Setup on local
1. Checkout the project repo
  1. ```git checkout https://github.com/.../opp_italia2 .```
1. Install & Run [Docker Toolkit](https://www.docker.com/products/docker-toolbox)
  1. If necessary *(for MacOS users)* run Kitematic, that will boot the Docker's VM
  1. Set the Docker's environment params
    1. Clear your last Docker environment ```$ eval "$(docker-machine env -u)"```
    1. Update environment params ```$ eval "$(docker-machine env default)"```
    1. Check the environment params ```$ env | grep DOCKER```
      - You should able to see the DOCKER_HOST & DOCKER_CERT_PATH params
1. Configure Environment settings
  1. Rename ```Environment.config.sample``` to ```Environment.config```
  1. Verify & update the ```Environment.config``` file with your credentials
  1. To check if everything is ok, run ```$ make env```
1. Run ```$ make help``` to check the possibility of make utility automated tasks
1. Run ```$ make up-dev``` to build & run *(start)* vm image *(can take a time)*
1. Run ```$ make deps``` to install *(or update)* the app dependencies
  - In case if you're get an memory issue with updating packagies, fire a ```make deps-refresh``` command to install dependencies from scratch
1. Run ```$ make logs``` to view all collected logs from app
1. Get your Docker VM ip with ```$ docker-machine ip```
1. Navigate to ```http://{docker-vm-ip}:8080/``` to check the app
1. Navigate to ```http://{docker-vm-ip}:8080/phpmyadmin/``` to access into mysql
1. Run ```$ make tests``` to run backend & FE tests
1. Run ```$ make assets``` to compile app assets
1. Run ```$ make deploy``` to deploy current version of app

### Setup on production
1. Checkout the codebase
1. Upload the up-to-date ```Environment.config``` file with **production** config
1. Upload the up-to-date ```production.yml``` file to ```site/src/config/```
1. Run ```$ make up``` to build & start container
1. Run ```$ make deps``` to install the app dependencies
1. Run ```$ make assets``` to compile & make assets up-to-date
1. Run ```$ make tests``` to be sure that everything will work properly
1. Navigate to your production url **(with port 80)**
  1. To manually set a port, add ```HTTP_PORT=1234``` variable before/after ```make``` command
1. To update an application codebase
  1. Run ```$ make sync``` to update the source codebase & rebuild assets
  1. Or do the same in manual way
    1. Run ```$ make git-up``` to update the source codebase
    1. Run ```$ make deps``` to update dependencies
    1. Run ```$ make precheck``` to fix the permission & stuff

### Technologies:
- **CentOS 6.7 64-bit (x86_64)** as a main OS
- [**Docker**](https://www.docker.com/products/docker-toolbox) for environment provisioner
- [**PHP** 5.5](http://php.net/manual/en/migration55.new-features.php)
- [phpMyAdmin 4.0.10](https://www.phpmyadmin.net/) to manage the mysql data
- [**Composer**](https://getcomposer.org/) as a dependency manager for PHP
- [**Bower**](http://bower.io/) as a dependency manager for frontend assets
- [**Angular Material**](https://material.angularjs.org/) as a MVC & UI on frontend
- [**LESS**](http://lesscss.org/) pre-process CSS with features

### Issues
- if case if you're got an directory permission issue, just run the ```make precheck``` that will set 0777 permissions for required directories
