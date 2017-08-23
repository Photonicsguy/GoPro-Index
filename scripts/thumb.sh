#!/bin/sh

frames=60;

if [ -e $1 ]
    thumb=`echo $1| sed -e 's/_strip/_thumb/'`
    then
    image=$1
    height=`convert $image -format "%h" info:`
    width=`convert $image -format "%w" info:`
    frame_width=$(($width/$frames))
    skip=$(($width/4))
#    echo $width
#    echo $frame_width
#    echo $skip
    convert "$image" -crop ${frame_width}x${height}+${skip}+0  +repage "$thumb"
fi
