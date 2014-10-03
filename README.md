DoctrineRest
============

[![Build Status](https://travis-ci.org/Tuxion/DoctrineRest.svg?branch=master)](https://travis-ci.org/Tuxion/DoctrineRest)
**This library is currently not stable. Do not use for production.**

Links Doctrine models to Aura.Router in a simple CRUD fashion.

### TODO

* Consider a PHP 5.3 compatible way of exposing JSON body rather than `\JsonSerialize`.
* Test for empty bodies at action.
* Test for null body in deleted results.
* Explicit test for ResultFactory.
* Helper functions for defining the routes -> models
* Integration (container) testing
* Implement pre and post methods
* Implement custom exceptions