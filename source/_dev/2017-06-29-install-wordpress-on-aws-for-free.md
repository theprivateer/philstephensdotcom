---
extends: _layouts.dev
section: content
date: 2017-06-29
title: Install Wordpress on AWS (for free)
---
# Install Wordpress on AWS (for free)

Whilst I'm gradually building out my own blogging platform to meet my specific needs, I still regularly have to spin up and maintain WordPress installations (both at [my previous role at iSeekplant](https://www.iseekplant.com.au/blog) and at my new gig at [Databee](https://databee.com.au)).  As more and more businesses move to 'the cloud', it's becoming increasingly typical to host blogs on infrastructures such as [Amazon Web Services](https://aws.amazon.com) and [DigitalOcean](https://www.digitalocean.com/).  DigitalOcean have more or less got you covered with one-click installers to spin up new server instances with everything ready to go, and AWSs slightly more consumer-friendly [Amazon Lightsail](https://amazonlightsail.com/) has similar offerings at a comparable price-point.  However if you're prepared to get your hands a little greasy and bypass the Lightsail interface you can take advantage of [AWSs free usage tier](https://aws.amazon.com/free/).

Whilst there are plenty of AMIs available on the AWS Marketplace to do this for you, I prefer a slightly more hand-on approach - plus, it really isn't that complicated.  Using AWSs free-usage tier, you can quickly get up and running with a custom WordPress blog on your own hosting, for free (well, for the first year at least).

### Launch a server

I'll assume that you've gone through the AWS registration (you will need a credit card - though it won't be charged) and verification process and are ready to go.

Log into your AWS account and go to the [EC2 dashboard](https://ap-southeast-2.console.aws.amazon.com/ec2/v2/home?region=ap-southeast-2#).

Click _Launch Instance_.

![](/assets/img/snapstack/1/TSdhvaleDD8EGgtvvVbT226d1f3txKNiudQqHl28.png)

Select the **Amazon Linux AMI 2017.03.1 (HVM), SSD Volume Type** (version correct at time of writing).

![](/assets/img/snapstack/1/JgajKGTinNm8wpU1ugpieTa6chwWWQbglvFroJqX.png)

For the purposes of this tutorial, the default selection (**t2.micro**) is fine (and eligible for the free usage tier!)

![](/assets/img/snapstack/1/RzfEHVm6H2oNs2HyDmaqKbT07hG3ZdUyG1qNlFLO.png)

Skip both the Step 3 (Configure Instance Details) and Step 4 (Add Storage) - they have sensible defaults.

In Step 5 (Add Tags) give your instance a name by clicking _click to add a Name tag_. Can't think of a name? Check out some fun suggestions at [https://haikunator.cloudstage.me/](https://haikunator.cloudstage.me/).

![](/assets/img/snapstack/1/9tieqvxe1x76RGrCJJJQYd8P5stszFCT0lCWMefP.png)

Next up, configure the security group.  For this tutorial we'll set up some very basic rules.  First off, lock down SSH access to your current IP address.  Then add a rule for HTTP on port 80 - set this to _Anywhere_.

![](/assets/img/snapstack/1/n0c46Miwk3jLuoyV789MdMgvb7aVXn9G046uS6dl.png)

Click _Review and Launch_.  This is the final step before we launch the new server instance - setting up an access key for SSH access to the server.  When prompted, select _Create a new key pair_ and give it a memorable name.  Download the key file (in .pem format) and keep it safe - this will be your only means to access the new server.

![](/assets/img/snapstack/1/Bqy3ywfpaU5S3KvVaoCI8CXHviY96f9SwvrKkcha.png)

Finally, click _Launch Instance_ to spin up your new server.

If you return to the instance overview you will be able to see your new EC2 instance initialising.  Make a note of the IPv4 Public IP address - you will need it next for logging onto the server.

In the first instance, to access the server (and your new WordPress installation) in a browser, you can either use the _Public DNS_ URL (something like _ec2-52-63-199-239.ap-southeast-2.compute.amazonaws.com_) or use a service such as [CloudStage](/cloudstage) to quickly create a more memorable staging domain - just enter your instance's IP address.

### Connect to your server instance

From now on we'll be using the command line to connect to and update your server.  Once your server is up and running open up Terminal (or similar) and navigate to where you have stored your `pem` file.  You'll need to update the file permissions before you can use it to ssh into your server:

```bash
chmod 400 wordpress.pem
```

Now you should be able to log into your box (substitute your server's IP address):

```bash
ssh -i wordpress.pem ec2-user@123.45.67.89
```

### Install Apache, PHP and MySQL

```bash
sudo yum update -y
```
```bash
sudo yum install -y httpd24 php56 mysql55-server php56-mysqlnd
```
```bash
sudo service httpd start
```

Navigate to your staging domain (or your server's public DNS) and you should see the default Apache page.

Since we don't want to have to manually start Apache in the event of a server reboot, you can use the `chkconfig` command to configure the Apache web server to start at each system boot.

```bash
sudo chkconfig httpd on
```

The chkconfig command does not provide any confirmation message when you successfully use it to enable a service. You can verify that httpd is on by running the following command:

```bash
chkconfig --list httpd
httpd           0:off   1:off   2:on    3:on    4:on    5:on    6:off
```

Here, httpd is on in runlevels 2, 3, 4, and 5 (which is what you want to see).

We won't bother setting up any virtual hosts in this tutorial, so everything for our site will go straight into the default directory `/var/www/html/`.

Set the file permissions:

```bash
sudo groupadd www
```
```bash
sudo usermod -a -G www ec2-user
```
```bash
exit
```
Log back in to your server (you can press the up arrow in your terminal to bring up the correct command).

Next we want to update the file permissions on the website root directory:

```bash
sudo chown -R root:www /var/www
```
```bash
sudo chmod 2775 /var/www
```
```bash
find /var/www -type d -exec sudo chmod 2775 {} \;
```
```bash
find /var/www -type f -exec sudo chmod 0664 {} \;
```

### Setting up MySQL

```bash
sudo service mysqld start
```
```bash
sudo mysql_secure_installation
```

* Press enter for the current root password
* Set a new root password
* Remove anonymous users - Yes
* Disallow root login remotely - Yes
* Remove test database and access to it? - Yes
* Reload privilege tables now - Yes

In order to complete your WordPress installation you'll need to set up a database, so let's do that now.

```bash
mysql -u root -p
```

Enter the root password that you set in the previous step.

```sql
CREATE DATABASE wordpress;
```
```sql
quit;
```

Finally, as we did with Apache, lets set MySQL to start automatically on system boot:

```bash
sudo chkconfig mysqld on
```

### Install WordPress

```bash
cd /var/www/html/
```
```bash
sudo wget https://wordpress.org/latest.zip
```
```bash
sudo unzip latest.zip
```
```bash
sudo mv wordpress/* ./
```
```bash
sudo rm latest.zip
```
```bash
sudo rmdir wordpress
```

Now you can visit your staging domain (or public DNS) and complete the installation.

You _may_ get an alert `Sorry, but I can't write the wp-config.php file` - if so run the following:

```bash
cd /var/www
```
```bash
sudo chown -R apache /var/www/html
```
```bash
cd html/
```
```bash
sudo find . -type d -exec chmod 0755 {} \;
```
```bash
sudo find . -type f -exec chmod 0644 {} \;
```
```bash
sudo service httpd restart
```

Refresh the browser and run the setup again - you should now be all set.

### Fix permalinks

In order for WordPress' pretty URL structure to work, you need to make some minor adjustments to your Apache setup:

```bash
sudo nano /etc/httpd/conf/httpd.conf
```

Find all instances of `Allowoverride None` and replace with `Allowoverride All`.  Save and exit, and then restart Apache for the changes to take effect:

```bash
sudo service httpd restart
```

You will now have a fully functioning installation of WordPress running on AWS!
