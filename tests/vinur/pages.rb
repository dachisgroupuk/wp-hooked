Given /^I am on the homepage$/ do
  visit_homepage()
  page.should have_css('.entry-title', :text => 'Layout Test')
end

When /^I click on the menu item "([^"]*)"$/ do |menu_item|
  click_link menu_item
end

Then /^I should see the page content$/ do
  # Page title
  page.should have_css('.entry-title', :text => 'Page with comments')
  page.should have_css('.entry-content', :text => 'This page has comments.')
  
end

Then /^I should see the comments posted$/ do
  page.should have_css('.comment-content', :text => 'Author comment.')
end