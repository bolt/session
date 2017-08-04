Bolt Session
============

PHP session handler built on Symfony components and supporting Silex v1 & v2.

Supports session storage with:

 - Doctrine cache
 - Symfony Filesystem
 - Bolt Filesystem
 - Memcache
 - Memcached
 - PSR-6 Cache
 - PSR-16 Simple Cache
 - Redis


Service Providers
-----------------

### Silex 1

```php
use Bolt\Session\Bridge\Silex1\SessionServiceProvider;
use Silex\Application;


$app = new Applicaiton();
$app->register(new SessionServiceProvider());
```


### Silex 2

```php
use Bolt\Session\Bridge\Silex2\SessionServiceProvider;
use Silex\Application;

$app = new Applicaiton();
$app->register(new SessionServiceProvider());
```


Browser cookies
---------------

By default, Bolt will inherit the settings `cookies_lifetime`, `cookies_domain`,
and `enforce_ssl` (for `cookie_secure`) should no override options be set, as
per the order of precedence explained in the introduction.

However, there are several override settings available, should you need more
fine-grained control.

### Life time

Time in seconds, that a cookie will be valid for. Setting this value to 0 means
"until the browser is closed".

| Key               | Default |              |
| ----------------- | ------- | ------------ |
| `cookie_lifetime` | 1209600 | Integer >= 0 |

In `.php.ini` this setting is [`session.cookie_lifetime`][php-cookie-lifetime].


### Base URI path

Specifies URI path to set in the session cookie.

| Key           | Default |              |
| ------------- | ------- | ------------ |
| `cookie_path` |     `/` | URI string   |

In `.php.ini` this setting is [`session.cookie_path`][php-cookie-path].


### Override domain name

Specifies the domain to set in the session cookie. Default is null, meaning the
host name of the server which generated the cookie.

| Key             | Default                   |                               |
| --------------- | ------------------------- | ----------------------------- |
| `cookie_domain` | HTTP(S) request host name | A fully qualified domain name |

In `.php.ini` this setting is [`session.cookie_domain`][php-cookie-domain].


### Enforce HTTPS requests

Setting this to `true` will enforce a HTTPS connection requirement to set, and
use, the session cookie.


| Key             | Default |                       |
| --------------- | ------- | --------------------- |
| `cookie_secure` | `false` | Boolean on/off toggle |

In `.php.ini` this setting is [`session.cookie_secure`][php-cookie-cookie-secure].


### Restricting request to the HTTP protocol

Marks the cookie as accessible only through the HTTP _protocol_, blocking
access to requests by things such as JavaScript.

This setting can effectively help to reduce identity theft through XSS attacks,
although browser support may vary.

| Key               | Default |                       |
| ----------------- | ------- | --------------------- |
| `cookie_httponly` | `true`  | Boolean on/off toggle |

Setting in your `config.yml`:

In `.php.ini` this setting is [`session.cookie_httponly`][php-cookie-httponly].


Session ID generation
---------------------

Session IDs are randomly generated to uniquely identify a user's session. Bolt
internally handles this generation in a fashion close to how PHP 7.1+ now does
to better ensure the uniqueness of the generated ID.

By default, both PHP & Bolt use an ID length of 32, which should provide only
a small chance of collisions, or predictability, of the generated ID.

On hosts with a consistent amount of available CPU resources, and a focus on
security, you should consider a number of 48 or greater. However, this will
increase the server load, and amount of time taken to generate session IDs.

An example of generating 1,000 session IDs on PHP 7.0 and an Intel i5-5200:

| ID length | milliseconds |
| ----------| ------------ |
|        32 |     0.002059 |
|        48 |     0.002560 |
|        64 |     0.003031 |
|       128 |     0.003016 |
|       256 |     0.004132 |


Maximum value supported is 256.

| Key          | Default |                          |
| -------------| ------- | ------------------------ |
| `sid_length` |      32 | Integer between 32 & 256 |

In PHP 7.1+ the `.php.ini` this setting is [`session.sid_length`][php-sid-length].


Session storage handler
-----------------------

Session storage handling, by default, is our filesystem layer. However, we also
support Redis & Memcached for more advanced use-cases.

| Key            | Default      |                                    |
| ---------------| ------------ | ---------------------------------- |
| `save_handler` | `filesystem` | `filesystem`, `redis`, `memcached` |

Setting in your `config.yml`:

In `.php.ini` this setting is [`session.save_handler`][php-save-handler].

**Note:** Some web hosting providers may implement alternative session handling
that is not compatible with Bolt Session.

Should you encounter exceptions from `SessionServiceProvider` indicating
problems with PHP's system save path, set `save_handler: filesystem`,
and the `save_path` option shown below.


### Using the Redis handler

When using Redis as the handler, the following options are also under the
`connections` subkey, of the session options:

| Key          | Default     |                                                   |
| ------------ | ----------- | ------------------------------------------------- |
| `host`       | `localhost` | Host name or I.P. address of Redis server         |
| `port`       |        6379 | TCP port of Redis server                          |
| `timeout`    |         0.0 | A float value in seconds (0.0 meanings unlimited) |
| `persistent` |      `null` | Boolean to toggle persistent connections          |
| `password`   |      `null` | (optional) Authenticate the connection using a password. **Warning:** The password is sent in plain-text over the network.
| `prefix`     |      `null` | (optional) Prefix string used on all keys         |
| `database`   |      `null` | Integer of the database index to connect to       |

If the native `\Redis` library is available, it will be used as the handler for
Redis, if not available, it will instead check for the PHP implementation of
the native library, `\Predis\Client` and use that.


### Using the Memcached handler

When using Memcached as the handler, the following options are also under the
`connections` subkey, of the session options:

| Key          | Default     |                                                       |
| ------------ | ----------- | ----------------------------------------------------- |
| `host`       | `localhost` | String host name or I.P. address of Memcached server  |
| `port`       |       11211 | TCP port of Memcached server                          |
| `weight`     |           0 | (optional) The weight of the server relative to the total weight of all the servers in the pool. This controls the probability of the server being selected for operations.
| `expiretime` |       86400 | (optional) Life time in seconds of stored keys        |
| `prefix`     |      `sf2s` | (optional) Prefix string used on all keys             |


Saved session file path
-----------------------

Session data is cached in between requests, and **is not** cleared by the
normal cache clearing functionality.

Instead, it uses garbage collection to manage deletion of expired sessions. See
the section below on garbage collection for details on configuration.

| Key         | Default             |                                   |
| ------------| ------------------- | --------------------------------- |
| `save_path` | `cache://.sessions` | Path passed to the `save_handler` |

**Note:** Manually deleting session data on a live server is **never advised**.
Should this ever be required on a live server, ensure all users are logged off,
and place the site into maintenance mode first.

In `.php.ini` this setting is [`session.save_path`][php-save-path].

### Using the Filesystem handler

When using the default filesystem handler, the `save_path` parameter needs to
be in the form of `{mount point}://{path}`.

See the [Overview of Bolt's Filesystem][bolt-filesystem] page for details on
the mount points available in Bolt.

**Warning:** If you set this to a world-readable directory, such as `/tmp`,
other users on the server may be able to hijack sessions, or extract
potentially sensitive data.


### Using the Redis handler

When using Redis as the handler, `save_path` should be defined in the format
`tcp://IPADDRESS:PORT`, with a default of `tcp://127.0.0.1:6379`.


### Using the Memcached handler

When using Memcached as the handler, `save_path` should be defined in the
format `IPADDRESS:PORT`, with a default of `127.0.0.1:11211`.


Garbage collection
------------------

Session garbage collection is the removal of sessions older than the configured
maximum life time.

The need to perform garbage collection is determined based on a random probability
calculation during the initialisation of each session.


### Maximum life time

The maximum life time setting specifies the number of seconds after which
session data will be seen as 'garbage' and potentially cleaned up.

| Key              | Default |                    |
| ---------------- | --------| ------------------ |
| `gc_maxlifetime` | 1209600 | Integer of seconds |

In `.php.ini` this setting is [`session.gc_maxlifetime`][php-gc-maxlifetime].


### Probability & divisor

The setting `gc_divisor` coupled with `gc_probability` define the probability that
the garbage collection (GC) process is performed.

In Bolt's session storage handler, the probability is calculated by generating
a random number between 0 and `gc_divisor`. If the value of `gc_probability` is
greater than the random number, garbage collection will be performed, and
session files older than the maximum configured life time are removed.

**Note:** To disable garbage collection, set `gc_probability` to `-1`.

| Key              | Default |             |
| ---------------- | --------| ----------- |
| `gc_probability` |       1 | Integer     |
| `gc_divisor`     |    1000 | Integer     |

In `.php.ini` these settings are:
  - [`session.gc_probability`][php-gc-probability]
  - [`session.gc_divisor`][php-gc-divisor]

---

[bolt-filesystem]: https://docs.bolt.cm/extensions/filesystem/introduction
[php-cookie-lifetime]: http://php.net/manual/en/session.configuration.php#ini.session.cookie-lifetime
[php-cookie-path]: http://php.net/manual/en/session.configuration.php#ini.session.cookie-path
[php-cookie-domain]: http://php.net/manual/en/session.configuration.php#ini.session.cookie-domain
[php-cookie-cookie-secure]: http://php.net/manual/en/session.configuration.php#ini.session.cookie-secure
[php-cookie-httponly]: http://php.net/manual/en/session.configuration.php#ini.session.cookie-httponly
[php-sid-length]: http://php.net/manual/en/session.configuration.php#ini.session.sid-length
[php-save-handler]: http://php.net/manual/en/session.configuration.php#ini.session.save-handler
[php-save-path]: http://php.net/manual/en/session.configuration.php#ini.session.save-path
[php-gc-maxlifetime]: http://php.net/manual/en/session.configuration.php#ini.session.gc-maxlifetime
[php-gc-probability]: http://php.net/manual/en/session.configuration.php#ini.session.gc-probability
[php-gc-divisor]: http://php.net/manual/en/session.configuration.php#ini.session.gc-divisor
