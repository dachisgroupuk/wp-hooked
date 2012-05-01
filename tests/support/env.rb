require 'ruby-debug'
require 'capybara'
require 'capybara/dsl'
require 'capybara/cucumber'
require 'selenium-webdriver'
require 'rspec/expectations'
require 'rack/test'
require 'yaml'
require 'capybara-webkit'

ENV['RACK_ENV'] = 'test'

Capybara.run_server = false
Capybara.default_host = "http://vinur.headshift.local"

# =======================
# Switch between drivers
# =======================

# Selenium 
# Capybara.default_driver = :selenium

# Webkit --headless
Capybara.default_driver = :webkit
Capybara.javascript_driver = :webkit