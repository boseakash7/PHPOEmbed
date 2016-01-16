# PHPOEmbed
Embed anything to your website in simple and easy way. Its is created by me because of a website I am creating and its a social network and it is my first open source script.
But you can use this script for any other kind of websites you will build. 

If you don't know any thing about **oEmbed** then you can read it from [oEmbed](http://www.oembed.com/).

This software requires PHP version 5.3+ to work.

## Features 
* It is pretty easy an simple.
* Add your own and custom providers.
* Almost returns any website as embed.
* All it require is two lines of code. Yeah! it is that simple. 

## Why use it
Sometimes you may want to embed any website simply from its url and every website does not provide oEmbed feature. 
So, here it may come in handy.

## How to use it
### Basic uses

Include **oembed.php** file.

```php
include 'oEmbed.php';
```

Now start typing your code.

```php
$url = 'http://example.com';
$oembed = new PHPOEmbed(); // line 1
$embed = $oembed->parse($url); // line 2
```
**$oembed->parse($url)** will return an encoded json string. So you may need to decode it.

```php
$de = json_decode($embed, true);
```

### Add custom providers
Go to **providers.php** to add your custom provider.

```php
$api = 'http://www.hulu.com/api/oembed.json?url=:url';
$pattern = '~hulu\.com/watch/.+~';
$key = 'hulu';
$provider = new PHPOEmbedProvider($api, $pattern, $key);
```
- **$api:** provide the api url for the provider. (Remember to use **:url** so that it can be replaced with the url you want to embed)
- **$pattern:** write a pattern for the url that it will match for this provider. 
- **$key:** give this provider an unique key. (It could be anything)

```php
//now add the provider
PHPOEmbed::addProvider($provider);
```

To see more about adding providers you can go to **providers.php** file. 
There I already added some of the providers and it may also help you to understand adding your own custom providers.
