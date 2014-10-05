CHANGELOG
=========

* 0.5 (2014-10-06)
 * c44b0f8 Fix url of phpunit.phar
 * 327bbf1 Option --ini is optional in CreateCommand.
 * 4bdd259 Fixing timstamp pattern to day with leading zeros in CreateCommand.
 * a8bcb44 Update symfony components
 * c181840 Adding help for create command.
 * fce3001 CreateCommand added.
 * e6e9605 Use ham crest composer package instead of ZIP file.
 * e6962de changed dbup detect autoloaders in accordance with its path for installation via Composer
 * a48e95f fixed the bin configuration of the composer.json is not pointed to a valid binary path
 * 7094502 updated dependent symfony components version
 * 2fa7362 added ability to replace %%DBUP_*%% keyword in .ini configuration file to an environment value which its key is matched
 * d19c840 add PHP 5.5, 5.6 to .travis.yml

* 0.4 (2013-05-09)

 * fdc292e Updated table format output
 * 1d7348a Added progress bar
 * b33e6cb Updated Symfony Component to v2.3-BETA1

* 0.3 (2013-05-03)

 * 9a4ec1a Fixed compile exclude tests files, so phar is lighter than before
 * a1e559c Fixed sql error when sql has empty lines #1
