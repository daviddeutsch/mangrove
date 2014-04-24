Why Mangrove?
=============

### It's not just "a faster horse"

It's actually a [motorized robospider](http://verraes.net/2014/01/henry-ford-fallacy/) for people who rock a fez.

In the past, you downloaded zip files (of libraries and/or applications) and installed them on your site or directly into your CMS.
Some of the more modern CMSs even allow for built-in installers and advanced features like automatic updates.

Now, we have composer, which more or less copies a bunch of repositories into a vendor directory and manages dependencies.
So the difference between the ancient days and today is that we have automated the downloading and uploading
and also a little bit of figuring out what things to download.

That's just a faster horse.

So I set out to cut down what I deliver to clients.

### It's not a firehose

After building applications as a monolithic package for 7 years, my installers would frequently grow beyond 4MB.
In the magic world of CMS hosting, that means your install breaks.

The real problem with that was that of those 4MBs, clients would typically use maybe 20%, so not only
were installs failing for clients, they were failing because I was handing them stuff they didn't need.

So a large portion of those 4MB was wasted traffic, plain and simple.

### It uses existing sources

Even though J! development can be somewhat insular at times, there is slowly a move towards using public libraries. Which results in every project packaging those libraries into their installer.

That's not a smart use of resources.

### It uses JSON

Because XML is serious overkill and makes unicorn ponies weep.
