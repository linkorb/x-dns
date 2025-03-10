X-DNS: Cross-provider DNS configuration management
==================================================

X-DNS allows you to:

* Manage your DNS configuration in git (enabling a pull-request based collaboration workflow)
* Pull DNS configuration from external providers
* Push DNS configuration from configuration files to external providers
* Diff DNS configurations between local and external providers
* Support dynamic DNS configuration
* Setup mirror domains at multiple target providers
* Migrate domain configuration from one provider to another
* Migrate configurations between multiple accounts at the same or different providers
* Consolidate domain configurations from multiple accounts

## Supported provider adapters

* TransIP: supported
* File: supported
* HTTP: planned
* AWS Route 53: planned
* CoreDNS: planned
* Digital Ocean: planned
* Scaleway: planned

(Contributions for these and other providers are welcome!)

## Installation

```php
$ git clone git@github.com:linkorb/x-dns.git
$ cd x-dns/
$ composer install # install dependencies
$ ./bin/x-dns help # run help command
```

## Configuration

You'll need to create an `x-dns.yaml` file listing your multiple provider accounts.

For example:

```yaml
providers:
  file:
    adapter: File
    path: config/

  transip-alice:
    adapter: TransIP
    username: alice
    key: |
      -----BEGIN PRIVATE KEY-----
      <snip>
      -----END PRIVATE KEY-----

  transip-bob:
    adapter: TransIP
    username: bob
    key: |
      -----BEGIN PRIVATE KEY-----
      <snip>
      -----END PRIVATE KEY-----
```

This file tells X-DNS that you have:

* one File-based provider, storing DNS configuration files in `config/`
* two TransIP accounts that you can use to pull, push and diff configurations from

## Security

The `x-dns.yaml` file should not be stored in git as-is, as it contains credentials for your external DNS providers. It is suggested to add this file to your `.gitignore` file, and/or to encrypt this file using a tool like [sops](https://github.com/getsops/sops) before committing it to git.

## File-based configuration files

When you add a `File`-based provider (named `file` in the example `x-dns.yaml` above), you can write YAML-based zone configuration files in a directory of your choice. In the example it's looking in the `config/` directory.

In that path, you can store multiple .yaml (or .yml) files containing zone configurations. For example:


```yaml
# example.org.yaml
name: example.org

includes:
    - other1.yaml # include other yaml files to add more records
    - other2.yaml

targets:
    - transip-alice # list the providers where these records are sync'ed with
records:
    -
        name: www
        type: A
        value: 93.184.215.14
        ttl: 300
    -
        name: mail
        type: A
        value: 93.184.215.14
        ttl: 300
    -
        name: "@10"
        type: MX
        value: mail.example.org.
        ttl: 300
```


## Usage

```sh
# list configured providers from x-dns.yaml
$ bin/x-dns provider:list
Providers: 3
  - file
  - transip-alice
  - transip-bob

# list all localy configured zones
$ bin/x-dns zone:list file
example.org
example.com

# list all externally configured zones
$ bin/x-dns zone:list transip-alice
example.org
hello.world

# show zone configuration of local file or external provider
$ bin/x-dns zone:show example.org@file
$ bin/x-dns zone:show example.org@transip-alice

# diff configuration between local config file and remote provider
./bin/x-dns zone:diff example.org@file transip-alice

# diff configuration between local config file and all target remote provider accounts of the zone
./bin/x-dns zone:diff example.org@file

# push configuration from local config file to remote provider
./bin/x-dns zone:push example.org@file transip-alice

# push configuration from local config file to all target remote provider accounts of the zone
./bin/x-dns zone:push example.org@file
```


