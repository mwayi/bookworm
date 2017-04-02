# Setting up dev5

[TOC]

As it stands dev5 has the most advanced configuration and workflow for CI2.

- pthreads installed
- php5.6
- composer
- git
- document root allowing us to hit `app/index.php`. This means that we do not need conditional url paths for ajax queries to work.
- ssh user
- corrent file ownership

If cloning takes place, dev5 should be the blueprint.


## Install instructions
In the absence of any of the above, below are some guides to help get the server and codebase up to speed.


### 1. SSH into server 

	ssh username@172.24.32.x
	
### 2. Add SSH key

In order to clone the repository we need to create an ssh key.

[Add ssh key instructions](https://help.github.com/articles/generating-a-new-ssh-key-and-adding-it-to-the-ssh-agent/)


	ssh-keygen -t rsa -b 4096 -C "admin@testing.com"
	
	eval "$(ssh-agent -s)"
	
	ssh-add ~/.ssh/id_rsa
	

Pbcopy may not exists so use vim to open the editor to copy on to the clipboard that way.
	
	vim ~/.ssh/id_rsa.pub
	
Log into github and under your user account add the new ssh key. Label it under the convention:-

	testing DevXX
	
### 3. Clone the repository

Once an ssh key has been set up you can now clone the repository.

	cd /var/www
	git clone git@github.com:testing/core.git testing


### 4. Symlink public roots

After cloning, environments will have a doc root of `/var/www/html`. We are unable to change this via config settings `/etc/httpd/conf/httpd.conf` as apache crashes.

Instead, point the html directory to the public root of the codebase. 

	cd /var/www
	ln -s /var/www/testing/Core/app html
	
	// check all is well
	
	ls -al
	
### 5. Folder ownership

We need to ensure that apache has access to the codebase. By default it will be root.

	chown -R apache:apache /var/www/testing

### 6. Composer install

#### Install composer

[Download composer instructions](https://getcomposer.org/download/)

- Install composer locally `php composer-setup.php --install-dir=bin --filename=composer`
- Proceed to install globally `mv composer.phar /usr/local/bin/composer`


#### On deployment

The application depends on composer and will throw an exception if it does not exist. Run 

	cd /var/www/testing/Core/app/
	composer install
	
On subsequent deployments you will need to:-

	composer update
	

### 7. Create repos directory

The application becomes buggy without the `repos/rtf` directory present. 

	cd /var/www/testing/test
	mkdir -p repos/rtf
	

### 8. Dot Env

Dot env files are not checked into the repository to allow for localised config settings. The application will exit if there is no .env file. Add one.

	vim /var/www/testing/Core/app/.env

Below is a template of config settings

	config=test
	

#### Environment setup
	
| ENVIRONMENT | Description | Error Reporting | Bugsnag |
|---|---|---|---|
| local | Personal development | on | off |
| development | Collaborative development | on | on |
| staging | UAT/End user facing/Testing | off | on |
| production | Live | off | on |


## Fast track

Steps 6 - 8 can now be achived by running 

	cd /var/www/testing/app
	bash publish-app

See [here](https://github.com/testing/core/pull/38). I will additionally add steps 4 and 5 within this script. 

## Deployment strategy

From this day forth, we should come out of the habit of deploying via FTP. This means that we:-

- **MUST** have a local environment set up
- **MUST** channel all work through git
- **MUST** channel development work via branches

This means our workflow for deployment will look something like

	// ssh into server
	
	cd /var/www/testing/Core/
	
	git fetch --all && git checkout my-pushed-branch
  
	// do some testing 
  