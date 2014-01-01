Why Mangrove?
=============

### It's not just "a faster horse"

It's actually a [motorized robospider](http://verraes.net/2014/01/henry-ford-fallacy/) for people who rock a fez.

In the past, you downloaded zip files and install them into Joomla. Now, with Joomla 3.0, you can have them automatically update. That's just a faster horse.

### It's not a firehose

After building AEC as a monolithic package for 6 years, the installer had grown to over 4MB. The install process took so long, that it would start breaking on smaller hosts.

They could run AEC without a problem, but installing it was a pain. Why? Because we always installed *everything*. Meaning all processors, all integrations, all translations.

This means that a good portion of those 4MB was wasted traffic, plain and simple.

### It uses existing sources

Even though J! development can be somewhat insular at times, there is slowly a move towards using public libraries. Which results in every project packaging those libraries into their installer.

That's not a smart use of resources.

### It uses JSON

Because XML is serious overkill and makes unicorn ponies weep.
