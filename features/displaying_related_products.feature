@displaying_related_products
Feature: Displaying related products
  In order to decide if I want to buy a related product
  As a Visitor
  I want to see products related to the one I'm currently browsing

  Background:
    Given the store operates on a single channel in "United States"
    And the store has a product "Gundam RX-78-2"
    And the store has a product "Zaku Amazing"
    And the store has a product "Zaku Regular"
    And the store has a product "Zaku Terrible"

  @ui @api
  Scenario: Displaying related products on product page
    Given there were 3 orders with products "Gundam RX-78-2" and "Zaku Terrible"
    And there were 5 orders with products "Gundam RX-78-2" and "Zaku Regular"
    And there were 11 orders with products "Gundam RX-78-2" and "Zaku Amazing"
    And the data is populated to Elasticsearch
    When I view product "Gundam RX-78-2"
    Then I should see related products list with the following products:
      | name           |
      | Zaku Amazing   |
      | Zaku Regular   |
      | Zaku Terrible  |
