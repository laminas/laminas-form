# laminas-form

[![Build Status](https://travis-ci.com/laminas/laminas-form.svg?branch=master)](https://travis-ci.com/laminas/laminas-form)
[![Coverage Status](https://coveralls.io/repos/github/laminas/laminas-form/badge.svg?branch=master)](https://coveralls.io/github/laminas/laminas-form?branch=master)

laminas-form is intended primarily as a bridge between your domain models and
the View Layer. It composes a thin layer of objects representing form elements,
an InputFilter, and a small number of methods for binding data to and from the
form and attached objects.

## Installation

Run the following to install this library:

```bash
$ composer require laminas/laminas-form
```

## Documentation

Browse the documentation online at https://docs.laminas.dev/laminas-form/

### Contributing

The documentation for the repository is available in the "**docs/**" directory.
It is written in [Markdown format] and built using [MkDocs].

#### Testing Your Changes Locally

We strongly encourage you to test your changes locally before contributing them.
However, to do so, you need to do three things; these are:

1. Have MkDocs installed
2. Have the Laminas documentation theme available to the laminas-form directory.
3. Update `mkdocs.yml` to build the docs using the documentation theme.

To complete these steps, please follow the instructions below.

1. [Install MkDocs]
2. Clone the [documentation-theme repository] locally.
    ```
    git clone git@github.com:laminas/documentation-theme.git documentation-theme
    ```
3. Copy the theme directory from the cloned documentation-theme repository to the root directory of this project.
4. Replace lines 91 and 92 of `mkdocs.yml` with the following Yaml snippet:
    ```yaml
    repo_url: 'https://github.com/laminas/laminas-form'
    extra:
        repo_name: laminas/laminas-form
        project: Components
        base_url: https://docs.laminas.dev/
        project_url: https://docs.laminas.dev/components/
    theme:
        name: null
        custom_dir: 'theme/'
        static_templates:
          - pages/404.html
    markdown_extensions:
        - pymdownx.superfences:
        - pymdownx.tabbed:
        - toc:
            toc_depth: 2
    ```

When you have completed all of these steps then, from the root directory of the project, run `mkdocs serve &`.
This builds the documentation locally, making it available on `http://127.0.0.1:8000`, if the port is not already in use, and actively watch for changes.
If changes are detected, the documentation is automatically regenerated, saving you the trouble of having to do so manually.

You should see initial output similar to the following if the process is successful.

```console
INFO    -  Building documentation...
INFO    -  Cleaning site directory
INFO    -  Documentation built in 2.47 seconds
[I 200430 22:06:08 server:296] Serving on http://127.0.0.1:8000
INFO    -  Serving on http://127.0.0.1:8000
[I 200430 22:06:08 handlers:62] Start watching changes
INFO    -  Start watching changes
[I 200430 22:06:08 handlers:64] Start detecting changes
INFO    -  Start detecting changes
```

If you have any trouble with these steps, ask for help in [the Laminas Slack #documentation channel].

## Support

* [Issues](https://github.com/laminas/laminas-form/issues/)
* [Chat](https://laminas.dev/chat/)
* [Forum](https://discourse.laminas.dev/)

[documentation-theme repository]: https://github.com/laminas/documentation-theme
[Install MkDocs]: https://www.mkdocs.org/#installation
[MkDocs]: https://www.mkdocs.org/
[Markdown format]: https://www.markdownguide.org/
[the Laminas Slack #documentation channel]: https://laminas.slack.com
