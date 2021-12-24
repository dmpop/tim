#!/usr/bin/env bash
sudo apt-get install cmake autoconf automake libtool nasm make pkg-config git libpng-dev
git clone https://github.com/mozilla/mozjpeg.git
cd mozjpeg
sudo cmake -G"Unix Makefiles" 
sudo make install
cd
git clone git@github.com:danielgtaylor/jpeg-archive.git
cd jpeg-archive
make