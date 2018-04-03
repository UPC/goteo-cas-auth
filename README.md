# CAS Authentication Plugin

Activating and properly configuring this plugin enables the ability to
authenticate users through a CAS server.

## Installation

The plugin must be installed inside the extend directory inside goteo
source tree:

```
cd extend
git clone https://github.com/UPC/goteo-cas-auth.git cas-auth
```

## Requirements

Package `jasig/phpCAS` is required by this plugin. You can use composer
to install it:

```
$ composer update jasig/phpCAS
```

## Settings

Configuration must be done in the general `config/settings.yml` file:

```yaml
...

plugins:
    cas-auth:
        active: true
        # Configuration options for the plugin goes here:
        version: "2.0"
        hostname: "cas.example.org"
        port: 443
        ca_cert: ""
        uri: ""
        verbose: false
        mail_domain: "example.org"
...

```

## References

* [Extending Goteo](http://goteofoundation.github.io/goteo/docs/developers/extend.html)
