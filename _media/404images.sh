#!/bin/sh


convert MissingThumbnail.png ../cache/404thumb.jpg
convert MissingStrip.png MissingStrip.png MissingStrip.png MissingStrip.png MissingStrip.png +append a.png
convert a.png a.png a.png a.png +append b.png
convert b.png b.png b.png +append ../cache/404strip.jpg

rm a.png b.png
