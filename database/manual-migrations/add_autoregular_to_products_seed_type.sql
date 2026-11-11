-- Manual migration for hosting: add Autoregular (AR) to products.seed_type.
-- Safe to run once. Does not change other tables/columns or existing data.
-- Keeps existing ENUM values A, F, R and adds AR.

ALTER TABLE `products`
  MODIFY `seed_type` ENUM('A', 'F', 'R', 'AR') NULL;
