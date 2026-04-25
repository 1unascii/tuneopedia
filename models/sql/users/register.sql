INSERT INTO user (first_name, last_name, user_name, email, password)
VALUES (:first_name, :last_name, :user_name, :email, UNHEX(SHA1(:password)))
