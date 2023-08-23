## Name

🧌 API MHRISE

## Context

This is a personal project. Its purpose was to:
- make available an API based on the Monster Hunter Rise universe
- (re)discover the basics of API Platform (especially for a professional project)

## Description

This project contains :
- Symfony project configuration
- Docker configuration
- Makefile
- Many tools for development

### Symfony project configuration

This project is based on my [Symfony 6.3 skeleton](https://github.com/emilien-gts/symfony-skeleton).

## Installation

With the Makefile, you just need to make a :

    make dc-install

To do this, you need to install [make](https://doc.ubuntu-fr.org/ubuntu-make).

## How to use ?

Once installed, you can run the synchronization command :

    make synchronize

This command will sycnhronize the JSONs I've retrieved (see source below) with the database.

You'll then be able to find API routes to consume / enrich the database:

- Monsters
- Quests
- Items
- Decorations
- Skills
- Weapons
- Armors


## Source

I owe my data (JSONs) to two Github repositories:

- https://github.com/CrimsonNynja/monster-hunter-DB (Only MHRISE data)
- https://github.com/Badge87/MHRiseScraperData

To find out where they get their JSONs from, I invite you to visit their repository.
Thanks to them for the data source, which enabled me to build this API.


## Project Status

The project is underway. I come back to it from time to time. I'm open to modifications to the data/source code (being a junior, I'm also open to advice on the source code).

Next step:
- Add more tests
- Add the POSTMAN collection to facilitate use of the API