---
date: 2021-07-24
title: Introducing PHP 8 Appreciate
author: Aydin Hassan, Michael Woodward
author_link: https://twitter.com/PHPSchoolTeam
---

_PHP School is a community based learning platform that will teach you the core skills of PHP using command line based interactive workshops._ 

PHP 8 Appreciate is our newest workshop demonstrating the new features of the latest PHP release. In this post we will teach you how to install and run the workshop, solving challenging problems with code as you go. We also dive in to how the workshop verifies your solutions and what to expect from the future. 

It’s been a little while since the initial PHP 8 release but [we know you’ve not all made the jump just yet](https://www.jetbrains.com/lp/devecosystem-2021/php/#PHP_which-version-of-php-do-you-regularly-use) 😄.

![PHP8 Appreciate Terminal](https://user-images.githubusercontent.com/2817002/124182783-3fd52f80-daaf-11eb-973a-82ae81451efc.png)

## What the Workshop Covers

PHP 8 Appreciate covers the majority of new additions that were introduced with PHP 8: 

* [Constructor Property Promotion](https://wiki.php.net/rfc/constructor_promotion)
* [Match Expression](https://wiki.php.net/rfc/match_expression_v2)
* [Throw Expression](https://wiki.php.net/rfc/throw_expression)
* [Non Capturing Catches](https://wiki.php.net/rfc/non-capturing_catches)
* [Mixed Type](https://wiki.php.net/rfc/mixed_type_v2)
* [Named Arguments](https://wiki.php.net/rfc/named_params)
* [Union Types](https://wiki.php.net/rfc/union_types_v2)
* [fdiv() Function](https://github.com/php/php-src/pull/4769)
* [Nullsafe Operator](https://wiki.php.net/rfc/nullsafe_operator)
* [Stringable Interface](https://wiki.php.net/rfc/stringable)
* [New String Functions: ](https://www.php.net/manual/en/ref.strings.php)[str_contains](https://wiki.php.net/rfc/str_contains), [str_starts_with and str_ends_with](https://wiki.php.net/rfc/add_str_starts_with_and_ends_with_functions)
* [Static Return Type](https://wiki.php.net/rfc/static_return_type)
* [Attributes](https://wiki.php.net/rfc/attributes_v2)

As usual, a major release of PHP comes with a variety of improvements to the language that developers can immediately benefit from without having to learn anything new, unfortunately it’s hard to incorporate these into a workshop as many don’t change the way you code

## How to Get Started

### 1. Install Workshop Manager

All workshops are installed and managed with `workshop-manager`, so first we need to install that (that is, if you don't already have it):

```shell
curl -O https://php-school.github.io/workshop-manager/workshop-manager.phar
mv workshop-manager.phar /usr/local/bin/workshop-manager
chmod +x /usr/local/bin/workshop-manager
```

Then you can run `workshop-manager verify` and it will alert you to any things that you might need to fix. Most likely, you will need to add `~/.php-school/bin` to your PATH environment variable.

### 2. Install PHP 8 Appreciate

Now that you have `workshop-manager` installed, you can use it to install workshops, including PHP 8 Appreciate:

```shell
workshop-manager install php8appreciate
```

### 3. Run PHP 8 Appreciate

The workshop can be started by typing `php8appreciate` and hitting enter.

![PHP8 Appreciate Terminal](https://user-images.githubusercontent.com/2817002/124182783-3fd52f80-daaf-11eb-973a-82ae81451efc.png)

## Pick an Exercise and Start Solving the Problem

The exercises have been put in an order we believe increases in difficulty as you go along. Don’t worry though, some things may click easier than others.

Select an exercise, using your arrow keys to navigate, and enter to select. Read the problem (along with any hints and tips) and try to put together your own solution. 

You will create and edit your solution using your preferred text editor. 

Some exercises will present you with files which contain bugs or starting points for solutions, they will be created in the directory you run `php8appreciate` from. If you want to keep organised, you could create a directory for your solutions and run `php8appreciate` from there.

When you want to verify your solution, you pass it as a command line argument to the verify command:

```shell
php8appreciate verify my-solution.php
```

If you are unsuccessful, you will be presented with hints and information describing the problem, so you can rectify and try again.

To execute your program and see the output without verifying it, you can use the `run` command like so:

```shell
php8appreciate run my-solution.php
```

This allows the exercise to pass arguments to your program and do other setup tasks. It will help iron out bugs in your solution.

## How we Verify

There are lots of techniques we use to verify the correctness of your solution. Each exercise decides how thorough the checks should be. Some will simply verify that the output of your program is the same as the expected output. This in itself is more complicated than it sounds since arguments which might be passed to your program can be, and are, random.

For more advanced checks, we parse your solution in to an AST and use that to verify that certain functions are called, variables are named correctly and classes are defined.

We check that you have required necessary dependencies with composer, have created required files and their contents are correct.

For some exercises, we even inject code to your solutions such as creating variables populated with data and wrapping code in `try/catch` statements!

_If you're interested in how all this works and want to get involved building workshops, [check out the docs](/docs)._

## What are you waiting for!

We’re excited to see how you get along, so please get in touch with us on [Twitter @PHPSchoolTeam](https://twitter.com/phpschoolteam). Any appreciation for our pun skills will also be warmly received! 

Look out for a deep dive article coming soon and more workshops to follow (PHP 8.1 beta1 was just tagged 😏) !

_Check out the other PHP School workshops on our homepage: [www.phpschool.io](/)_
