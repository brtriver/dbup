# More info at https://github.com/guard/guard#readme
 
guard 'shell' do
  watch(/(src|tests)\/(.*).php/) {|m| `php phpunit.phar --configuration phpunit.xml.dist --tap --colors` }
end
