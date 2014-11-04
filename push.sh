#!/bin/bash

app/console cache:clear && rm -fR app/cache/* app/logs/* && git add --all * && git commit -a -m "$1" && git push