<?php

    class Beer {

        private $beer_name;
        private $style;
        private $abv;
        private $ibu;
        private $container;
        private $brewery;
        private $image;
        private $id;

      function __construct($beer_name, $style, $abv, $ibu, $container, $brewery, $image, $id=null)
      {
          $this->beer_name = $beer_name;
          $this->style = $style;
          $this->abv = $abv;
          $this->ibu = $ibu;
          $this->container= $container;
          $this->brewery = $brewery;
          $this->image = $image;
          $this->id = $id;
      }

      function setBeerName($new_beer_name)
      {
          $this->beer_name = (string) $new_beer_name;
      }

      function getBeerName()
      {
          return $this->beer_name;
      }

      function setStyle ($new_style)
      {
          $this->style = (string) $new_style;
      }

      function getStyle()
      {
          return $this->style;
      }

      function setAbv($new_abv)
      {
          $this->abv = (string) $new_abv;
      }

      function getAbv()
      {
          return $this->abv;
      }

      function setIbu($new_ibu)
      {
          $this->ibu = (string) $new_ibu;
      }

      function getIbu()
      {
          return $this->ibu;
      }

      function setContainer($new_container)
      {
          $this->container = (string) $new_container;
      }

      function getContainer()
      {
          return $this->container;
      }

      function setBrewery($new_brewery)
      {
          $this->brewery = (string) $new_brewery;
      }

      function getBrewery()
      {
          return $this->brewery;
      }

      function setImage($new_image)
      {
          $this->image = $new_image;
      }

      function getImage()
      {
          return $this->image;
      }

      function getId()
      {
          return $this->id;
      }


      function save()
      {
          $GLOBALS['DB']->exec("INSERT INTO beers (beer_name, style, abv, ibu, container, brewery, image) VALUES ('{$this->getBeerName()}', '{$this->getStyle()}', {$this->getAbv()}, {$this->getIbu()}, '{$this->getContainer()}', '{$this->getBrewery()}', '{$this->getImage()}');");
           $this->id = $GLOBALS['DB']->lastInsertId();
      }

      //Begin static functions
      static function getAll()
      {
          $returned_beers = $GLOBALS['DB']->query("SELECT * FROM beers;");
          $beers = array();
          foreach($returned_beers as $beer) {
              $beer_name = $beer['beer_name'];
              $style = $beer['style'];
              $abv = $beer['abv'];
              $ibu = $beer['ibu'];
              $container = $beer['container'];
              $brewery = $beer['brewery'];
              $image = $beer['image'];
              $id = $beer['id'];
              $new_beer = new Beer($beer_name, $style, $abv, $ibu, $container, $brewery, $image, $id);
              array_push($beers, $new_beer);
          }
          return $beers;
      }

      static function deleteAll()
      {
          $GLOBALS['DB']->exec("DELETE FROM beers;");
      }

    //   static function find($search_id)
    //   {
    //       $found_beer = null;
    //       $beers = Beer::getAll();
    //       foreach($beers as $beer) {
    //           $beer_id = $beer->getId();
    //           if ($beer_id == $search_id) {
    //               $found_beer = $beer;
    //           }
    //       }
    //       return $found_beer;
    //   }

      //Searching for beers in database with column_id as a variable example: Beer::find("abv", $search_id);
      static function find($column_id, $search_id) {
          //$column_id is what column to search, example user_id etc
          //if $search_id is an ID or review_date, it will be a string, else it will be an int
          if (is_string($search_id)) {
              $search_beers = $GLOBALS['DB']->query("SELECT * FROM beers WHERE {$column_id} = '{$search_id}'");
          }
          else {
              $search_beers = $GLOBALS['DB']->query("SELECT * FROM beers WHERE {$column_id} = {$search_id}");
          }
          $returned_beers = $search_beers->fetchAll(PDO::FETCH_ASSOC);
          $beers = array();
          foreach($returned_beers as $beer) {
              $beer_name = $beer['beer_name'];
              $style = $beer['style'];
              $abv = $beer['abv'];
              $ibu = $beer['ibu'];
              $container = $beer['container'];
              $brewery = $beer['brewery'];
              $image = $beer['image'];
              $id = $beer['id'];
              $new_beer = new Beer($beer_name, $style, $abv, $ibu, $container, $brewery, $image, $id);
              array_push($beers, $new_beer);
          }
          return $beers;
      }

      //Searching unique stores table with column_id as a variable
      static function findDistinct($column_id) {
          $search_beers = $GLOBALS['DB']->query("SELECT DISTINCT {$column_id} FROM beers");
          $returned_beers = $search_beers->fetchAll(PDO::FETCH_ASSOC);
          $unique_column_ids = array();
          foreach($returned_beers as $beer) {
              $unique_column = $beer[$column_id];
              array_push($unique_column_ids, $unique_column);
          }
          return $unique_column_ids;
      }

      //Begin add and find user functions
      function addUser($user_id)
      {
          if(Review::findReview($this->getId(), $user_id) == false) {
              $GLOBALS['DB']->exec("INSERT INTO reviews (beer_id, user_id) VALUES ({$this->getId()}, {$user_id});");
          }
      }


      function getUsers()
      {
          $beer_id = $this->getId();
          $returned_users = $GLOBALS['DB']->query("SELECT users.* FROM beers JOIN reviews ON (beers.id = reviews.beer_id) JOIN users
                        ON (reviews.user_id = users.id) WHERE beers.id = {$this->getId()}");
          $users = array();
          foreach($returned_users as $user) {
              $user_name = $user['user_name'];
              $preferred_style = $user ['preferred_style'];
              $region = $user ['region'];
              $id = $user['id'];
              $new_user = new User($user_name, $preferred_style, $region, $id);
              array_push($users, $new_user);
          }
          return $users;
      }



      //Begin store functions
      function addStore($store_id)
      {
          $GLOBALS['DB']->exec("INSERT INTO beers_stores (beer_id, store_id) VALUES ({$this->getId()}, {$store_id});");
      }

      function getStores() {
          $query = $GLOBALS['DB']->query("SELECT stores.* FROM beers
            JOIN beers_stores ON (beers.id = beers_stores.beer_id)
            JOIN stores ON (beers_stores.store_id = stores.id)
            WHERE beers.id = {$this->getId()};");
          $stores = $query->fetchAll(PDO::FETCH_ASSOC);
          $stores_array = array();
          foreach($stores as $store) {
              $id = $store['id'];
              $store_name = $store['store_name'];
              $category = $store ['category'];
              $region = $store ['region'];
              $address = $store['address'];
              $new_store = new Store($store_name, $category, $region, $address, $id);
              array_push($stores_array, $new_store);
          }
          return $stores_array;
      }

      //Update and delete functions
      function delete()
      {
          $GLOBALS['DB']->exec("DELETE FROM beers WHERE id = {$this->getId()};");
          $GLOBALS['DB']->exec("DELETE FROM reviews WHERE beer_id = {$this->getId()};");
          $GLOBALS['DB']->exec("DELETE FROM beers_stores WHERE beer_id = {$this->getId()};");
      }

      function update($new_beer_name, $new_style, $new_abv, $new_ibu, $new_container,
                     $new_brewery, $new_image)
       {
           $GLOBALS['DB']->exec("UPDATE beers SET beer_name = '{$new_beer_name}', style = '{$new_style}', abv = '{$new_abv}', ibu = '{$new_ibu}', container = '{$new_container}', brewery = '{$new_brewery}', image = '{$new_image}' WHERE id = {$this->getId()};");
           $this->setBeerName($new_beer_name);
           $this->setStyle($new_style);
           $this->setAbv($new_abv);
           $this->setIbu($new_ibu);
           $this->setContainer($new_container);
           $this->setBrewery($new_brewery);
           $this->setImage($new_image);
       }
    }
?>
