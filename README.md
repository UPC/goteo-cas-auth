# CAS Authentication Plugin

Activating and properly configuring this plugin enables the ability to
authenticate users through a CAS server.

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
