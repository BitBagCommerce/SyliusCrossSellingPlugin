@displaying_related_products_by_common_taxons
Feature: Displaying related products by common taxons
  In order to decide if I want to buy a related product
  As a Visitor
  I want to see other products with the same taxons as the one I'm viewing

  Background:
    Given the store operates on a single channel in "United States"
    And the store classifies its products as "Mobile Suits"
    And the "Mobile Suits" taxon has children taxons "Gundam" and "Zaku"
    And the store has a product "Zaku Amazing"
    And this product belongs to "Mobile Suits"
    And the store has a product "Zaku Regular"
    And this product belongs to "Mobile Suits"
    And the store has a product "Gundam RX-0"
    And the product "Gundam RX-0" belongs to taxon "Gundam"
    And the product "Gundam RX-0" belongs to taxon "Mobile Suits"
    And the store has a product "Gundam RX-78-2"
    And the product "Gundam RX-78-2" has a main taxon "Gundam"
    And the product "Gundam RX-78-2" belongs to taxon "Mobile Suits"

  @ui @api
  Scenario: Displaying related products on product page using common taxons
    When I view product "Gundam RX-78-2"
    Then I should see related products list with the following products:
      | name         |
      | Gundam RX-0  |
      | Zaku Amazing |
      | Zaku Regular |
