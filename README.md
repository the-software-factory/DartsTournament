# Darts Tournament #

Sample application developed for support during the UNIVPM seminar on May 28th.

## Prerequisites ##

In order to run the app, you will need:

- Composer, the PHP dependency manager: https://getcomposer.org/doc/00-intro.md#installation-linux-unix-osx (the "global" installation is recommended")
- Vagrant, a tool to manage and distribute portable development environments: http://docs.vagrantup.com/v2/installation/

## Set up ##

After cloning the repository, just download the dependencies:
```
$ composer install
```

And bring the virtual machine up:
```
$ vagrant up
```

The above is a step that needs to be performed every time you start working on the app. The first run will be slow (it needs to download the virtual machine base image), the subsequent ones will take just a few seconds.

When done, just reach the application at the following URL: `http://localhost:8080`.

## What it does ##

The Zend1 app manages a darts tournament which consists in *501 double out* games, letting you register scores for each player in each turn. At the end of the game, it increments a counter of games won for the winner.
Rules for 501 double out can be found here: http://www.flyordie.com/games/help/darts/en/game_rules_501.html

## App versions ##
* Version 1, "oh god why" - Most of the logic is in the controller, SOLID principles are all violated, it's impossible to write proper unit tests.
* Version 2, "meh" - Logic has been factored in several service classes, but there are no interfaces nor dependency injection whatsoever.
* Version 3, "not bad" - Services are now implementing interfaces and dependency injection is properly done via a service container. All units now depend upon abstractions.

## Slides ##
Below you can find the slides we created for the seminar @ UNIVPM. Italian only, sorry about that!

* https://docs.google.com/presentation/d/1AfmMSlg53j8Ut21iRmaW6r52eQsAXPKyKyt1X4ipWYE
* https://docs.google.com/presentation/d/1wt-zGrcRObJEhR9Zlh5142PFis9yNikP6mRA4ZOl_nU
