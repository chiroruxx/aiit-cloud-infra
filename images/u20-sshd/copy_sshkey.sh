#!/bin/bash

mkdir "$HOME"/.ssh
chmod 700 "$HOME"/.ssh

cp /metadata/id_rsa.pub "$HOME"/.ssh/authorized_keys
chmod 600 "$HOME"/.ssh/authorized_keys
