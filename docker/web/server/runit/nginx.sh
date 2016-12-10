#!/usr/bin/env bash

sv -w1 check phpfpm
exec nginx
