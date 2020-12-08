@displaying_related_products_by_mixed_criteria
Feature: Displaying related products by mixed criteria
  In order to decide if I want to buy a related product
  As a Visitor
  I want to see products related first by order history and then by taxons

  Background:
    Given the store operates on a single channel in "United States"
    And the store classifies its products as "Mobile Suits"
    And the store has a product "Gundam RX-78-2"
    And this product belongs to "Mobile Suits"
    And the store has a product "Zaku Amazing"
    And this product belongs to "Mobile Suits"
    And the store has a product "High-grade marker"
    And there were 1 orders with products "Gundam RX-78-2" and "High-grade marker"
    And the data is populated to Elasticsearch

  @ui @api
  Scenario: Displaying related products on product page using common taxons
    When I view product "Gundam RX-78-2"
    Then I should see related products list with the following products:
      | name              |
      | High-grade marker |
      | Zaku Amazing      |
