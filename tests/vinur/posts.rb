When /^I click on the readmore link of the article "([^"]*)"$/ do |article_name|
  page.click_link article_name
end

Then /^I should be redirected to the article "([^"]*)"$/ do |article_name|
  # Check for header 
  page.should have_css('.entry-title', :text => article_name)
  
  # Check for some random variables
  case article_name
  when 'Readability Test'
    page.should have_css('.entry-meta', :text => 'Posted on September 5, 2008 by Chip Bennett')
  end
end

Given /^I am on the homepage and not logged in$/ do
  visit_homepage()
  page.should_not have_css('#wpadminbar', :text => 'Howdy,')
end

Then /^I should not see the edit link$/ do
  page.should_not have_css('a.post-edit-link', :text => 'Edit')
end