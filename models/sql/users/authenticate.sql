SELECT * FROM user
WHERE user_name = :username AND password = UNHEX(SHA1(:password))
