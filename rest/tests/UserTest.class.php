<?php

use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public function testRegisterUserEmail()
    {
        // Create a mock HTTP request with user registration data

        $curl = curl_init();

        curl_setopt_array($curl, array(
          CURLOPT_URL => 'localhost/auctions-ba/rest/register',
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'POST',
          CURLOPT_POSTFIELDS =>'{
            "email": "ajdin.hukic@stu.ibu.edu.ba",
            "password": "test123",
            "username": "asd",
            "firstname": "Ajdin",
            "secondname": "Hukic"
        }',
        CURLOPT_HTTPHEADER => array(
          'Content-Type: application/json'
        ),
        ));

        $response = curl_exec($curl);
        $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        $responseData = json_decode($response, true);
        // Assert that the response has a 200 status code
        $this->assertEquals(500, $httpcode);
        // Assert that the response contains the expected success message
        $this->assertEquals('Email already registered', $responseData['message']);
    }

    public function testRegisterUserUserName()
    {
        // Create a mock HTTP request with user registration data

        $curl = curl_init();

        curl_setopt_array($curl, array(
          CURLOPT_URL => 'localhost/auctions-ba/rest/register',
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'POST',
          CURLOPT_POSTFIELDS =>'{
            "email": "ajdin.hukic@bu.edu.ba",
            "password": "test123",
            "username": "AjdiNNN",
            "firstname": "Ajdin",
            "secondname": "Hukic"
        }',
        CURLOPT_HTTPHEADER => array(
          'Content-Type: application/json'
        ),
        ));

        $response = curl_exec($curl);
        $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        $responseData = json_decode($response, true);
        // Assert that the response has a 200 status code
        $this->assertEquals(500, $httpcode);
        // Assert that the response contains the expected success message
        $this->assertEquals('Username already registered', $responseData['message']);
    }

    public function testLoginUser()
    {
        // Create a mock HTTP request with user registration data

        $curl = curl_init();

        curl_setopt_array($curl, array(
          CURLOPT_URL => 'localhost/auctions-ba/rest/login',
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'POST',
          CURLOPT_POSTFIELDS =>'{
            "email": "ajdin.hukiu.ibu.edu.ba",
            "password": "test123"
        }
        ',
          CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json'
          ),
        ));

        $response = curl_exec($curl);
        $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        $responseData = json_decode($response, true);
        // Assert that the response has a 200 status code
        $this->assertEquals(404, $httpcode);
        // Assert that the response contains the expected success message
        $this->assertEquals('User doesn\'t exist', $responseData['message']);
    }

    public function testLoginWrongPasswordUser()
    {
        // Create a mock HTTP request with user registration data

        $curl = curl_init();

        curl_setopt_array($curl, array(
          CURLOPT_URL => 'localhost/auctions-ba/rest/login',
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'POST',
          CURLOPT_POSTFIELDS =>'{
            "email": "ajdin.hukic@stu.ibu.edu.ba",
            "password": "test13"
        }
        ',
          CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json'
          ),
        ));

        $response = curl_exec($curl);
        $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        $responseData = json_decode($response, true);
        // Assert that the response has a 200 status code
        $this->assertEquals(404, $httpcode);
        // Assert that the response contains the expected success message
        $this->assertEquals('Wrong password', $responseData['message']);
    }

    public function testLoginTrueUser()
    {
        // Create a mock HTTP request with user registration data

        $curl = curl_init();

        curl_setopt_array($curl, array(
          CURLOPT_URL => 'localhost/auctions-ba/rest/login',
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'POST',
          CURLOPT_POSTFIELDS =>'{
            "email": "ajdin.hukic@stu.ibu.edu.ba",
            "password": "test123"
        }
        ',
          CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json'
          ),
        ));

        $response = curl_exec($curl);
        $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        $responseData = json_decode($response, true);
        // Assert that the response has a 200 status code
        $this->assertEquals(200, $httpcode);
        // Assert that the response contains the expected success message
        $this->assertTrue(isset($responseData['token']));
    }
}
