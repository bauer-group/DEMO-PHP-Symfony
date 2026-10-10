# [0.4.0](https://github.com/bauer-group/DEMO-PHP-Dockerized/compare/v0.3.5...v0.4.0) (2026-01-13)


### Features

* add .env file creation from template in entrypoint scripts ([e0a9116](https://github.com/bauer-group/DEMO-PHP-Dockerized/commit/e0a9116dca818a1043511f7499f795f5a6b7ba95))

## [0.4.2](https://github.com/bauer-group/DEMO-PHP-Symfony/compare/v0.4.1...v0.4.2) (2026-10-10)

### 🐛 Bug Fixes

* **docker:** built the AssetMapper assets into the production image ([23ce544](https://github.com/bauer-group/DEMO-PHP-Symfony/commit/23ce544dbf9daff1115a9cfa4575caefa5aefcde))
* **docker:** made the production cache warmup run and fail the build ([7c0b210](https://github.com/bauer-group/DEMO-PHP-Symfony/commit/7c0b210a81961621ff81f52290ced9061edc20db))
* **docker:** replaced the removed doctrine:query:sql in the entrypoint ([628ee27](https://github.com/bauer-group/DEMO-PHP-Symfony/commit/628ee27bf8ce7ab6722d873fb610648e778c7061))
* **doctrine:** removed the proxy_dir option dropped in DoctrineBundle 3 ([93267d6](https://github.com/bauer-group/DEMO-PHP-Symfony/commit/93267d69684ee8f6f18ee3ec58047f8fec773373))

### 🔧 Maintenance

* update Dockerfile version to 0.4.1 ([21f65d5](https://github.com/bauer-group/DEMO-PHP-Symfony/commit/21f65d50db29de7e096d386cae9c0fddc813cbd9))

## [0.4.1](https://github.com/bauer-group/DEMO-PHP-Symfony/compare/v0.4.0...v0.4.1) (2026-10-10)

### 🐛 Bug Fixes

* **ci:** added the missing permissions block ([facfc17](https://github.com/bauer-group/DEMO-PHP-Symfony/commit/facfc17917ba528917ee781fa9144cce6d8132b2))
* **composer:** updated twig, symfony and phpunit to patched versions ([#27](https://github.com/bauer-group/DEMO-PHP-Symfony/issues/27)) ([5510379](https://github.com/bauer-group/DEMO-PHP-Symfony/commit/5510379b15738928c5189cd410e9769d2ad3df55))

### 🔧 Maintenance

* **ci:** removed issue AI summary workflow ([432ed87](https://github.com/bauer-group/DEMO-PHP-Symfony/commit/432ed8712c616c0da3beee5703c569a4e4bafd0b)), references [bauer-group/automation-templates#105](https://github.com/bauer-group/automation-templates/issues/105)
* **ci:** removed redundant teams notification ([e3a7f57](https://github.com/bauer-group/DEMO-PHP-Symfony/commit/e3a7f5770b08e1dd74d8ce1721e863ed16476d94))
* **composer:** bump phpunit/phpunit ([36ccfee](https://github.com/bauer-group/DEMO-PHP-Symfony/commit/36ccfee67a8d207e2df57e8eaf49cea5af6e0126))
* update Dockerfile version to 0.4.0 ([647fbbe](https://github.com/bauer-group/DEMO-PHP-Symfony/commit/647fbbe6b87845fcf53f99e61549340b39837679))

## [0.3.5](https://github.com/bauer-group/DEMO-PHP-Dockerized/compare/v0.3.4...v0.3.5) (2026-01-13)


### Bug Fixes

* unify database volume naming across Docker Compose files ([2484db1](https://github.com/bauer-group/DEMO-PHP-Dockerized/commit/2484db13e454b51f945c1acf5f0b7130687bbe14))

## [0.3.4](https://github.com/bauer-group/DEMO-PHP-Dockerized/compare/v0.3.3...v0.3.4) (2026-01-13)


### Bug Fixes

* streamline asset installation error handling in entrypoint script ([69b2fbb](https://github.com/bauer-group/DEMO-PHP-Dockerized/commit/69b2fbb3ff347eee3eaec7266ee9aba832db49c9))

## [0.3.3](https://github.com/bauer-group/DEMO-PHP-Dockerized/compare/v0.3.2...v0.3.3) (2026-01-13)


### Bug Fixes

* add timeout handling for assets installation in entrypoint script ([42be772](https://github.com/bauer-group/DEMO-PHP-Dockerized/commit/42be7724bc81a172114f8cebfd26182fd1a4b4c8))

## [0.3.2](https://github.com/bauer-group/DEMO-PHP-Dockerized/compare/v0.3.1...v0.3.2) (2026-01-13)


### Bug Fixes

* update entrypoint script to sync Composer dependencies without auto-scripts and run asset installation separately ([52b9d4c](https://github.com/bauer-group/DEMO-PHP-Dockerized/commit/52b9d4cf46c41f2bd18ec21142bc0f433d8b5c9f))

## [0.3.1](https://github.com/bauer-group/DEMO-PHP-Dockerized/compare/v0.3.0...v0.3.1) (2026-01-13)


### Bug Fixes

* update Dockerfile to expose only HTTP port and enhance entrypoint script with timeout handling for importmap installation ([20d88c0](https://github.com/bauer-group/DEMO-PHP-Dockerized/commit/20d88c020f22942a54503f26688dcfb96b93db43))

# [0.3.0](https://github.com/bauer-group/DEMO-PHP-Dockerized/compare/v0.2.0...v0.3.0) (2026-01-13)


### Features

* add quick add functionality for todos and improve UI feedback ([eff88dd](https://github.com/bauer-group/DEMO-PHP-Dockerized/commit/eff88dd42a5c4c8abe25f7ce527f454df1c1db2e))
* enhance todo edit and new templates with improved card styling and button functionality ([65d7edc](https://github.com/bauer-group/DEMO-PHP-Dockerized/commit/65d7edc548cff1f00cf0db8137853bf7009ebca5))

# [0.2.0](https://github.com/bauer-group/DEMO-PHP-Dockerized/compare/v0.1.9...v0.2.0) (2026-01-13)


### Features

* integrate Symfony UX with Stimulus and Turbo bundles ([e23decc](https://github.com/bauer-group/DEMO-PHP-Dockerized/commit/e23deccc7f71ff75865fd86fa7bfbbb17b3435e4))

## [0.1.9](https://github.com/bauer-group/DEMO-PHP-Dockerized/compare/v0.1.8...v0.1.9) (2026-01-13)


### Bug Fixes

* add docker configuration to Symfony extra settings in composer.json ([dccbfa4](https://github.com/bauer-group/DEMO-PHP-Dockerized/commit/dccbfa44027cd85d98e656dc02e4961fd3f0216d))
* remove use_savepoints configuration from doctrine settings ([d073d20](https://github.com/bauer-group/DEMO-PHP-Dockerized/commit/d073d20507928479db8f3483084395d0eb48cb46))
* update dependencies in composer files and improve entrypoint script for syncing ([9462235](https://github.com/bauer-group/DEMO-PHP-Dockerized/commit/94622351c8b020691a438be9db10a7aaeb940e7a))

## [0.1.8](https://github.com/bauer-group/DEMO-PHP-Dockerized/compare/v0.1.7...v0.1.8) (2026-01-13)


### Bug Fixes

* disable caching due to connectivity issues with GitHub's cache infrastructure ([3a6bf7d](https://github.com/bauer-group/DEMO-PHP-Dockerized/commit/3a6bf7d5369ffc6fdf9ec38d3c8ad309e9b72f25))
* disable caching for Docker build and PR validation ([58d8fa7](https://github.com/bauer-group/DEMO-PHP-Dockerized/commit/58d8fa734da40b6a7e2fbe4bf5fdb4a58b4a0e64))
* update Symfony dependencies to version 8.0.* ([d66773e](https://github.com/bauer-group/DEMO-PHP-Dockerized/commit/d66773ec4c9e3a15eb221dd7c5ad5a3441877694))

## [0.1.7](https://github.com/bauer-group/DEMO-PHP-Dockerized/compare/v0.1.6...v0.1.7) (2026-01-13)


### Bug Fixes

* XDebug Dependencies ([160f929](https://github.com/bauer-group/DEMO-PHP-Dockerized/commit/160f929bd69afc4fb06cf14ca56d6c53e6f1041f))

## [0.1.6](https://github.com/bauer-group/DEMO-PHP-Dockerized/compare/v0.1.5...v0.1.6) (2026-01-13)


### Bug Fixes

* update Xdebug installation and configuration for PHP 8.5 support ([91d79af](https://github.com/bauer-group/DEMO-PHP-Dockerized/commit/91d79afc6a341b98b83bf0ead3183204bb77a076))

## [0.1.5](https://github.com/bauer-group/DEMO-PHP-Dockerized/compare/v0.1.4...v0.1.5) (2026-01-13)


### Bug Fixes

* install Xdebug 3.5.0 from source for PHP 8.5 support ([73a5791](https://github.com/bauer-group/DEMO-PHP-Dockerized/commit/73a5791d664be021161b5656233ccc6e52d75c3f))
