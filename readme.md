# Campaign Organiser

This, slightly over-the-top named project, is a product of immediate need. It parses `*.md` files and displays them in simple CSS formatting.

## Usage

Place your `*.md` files in `data/` directory and they will be accessible as pages. `index.md` will act as page index.

It is possible to store files in directories and still access them. `data/example/example.md` would be accessible under `[server path]/example-example`. The side effect is impossibility of using `-` character in filenames, unless the file is in main `data/` directory.

## Development

* Configurability for project
* Config file for data
* Better handling of versioning and titles

### Optional goals

* Better file search algorithm that makes `-` in filenames possible
* Addition of real directory structure with proper routing