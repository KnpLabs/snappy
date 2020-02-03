
# Thanks for contributing!

:+1: First of all, thanks for contributing! The team is happy to help if you
have any questions. Have a look to this contributing guide and also to the
[FAQ section](https://github.com/KnpLabs/snappy/blob/master/doc/faq.md). :feet:
The following is a set of guidelines for contributing to Snappy, which is hosted
by the [KNP Labs Organization](https://github.com/KnpLabs) on GitHub. These are
mostly guidelines, not rules. Use your best judgment, and feel free to propose
changes to this document opening a pull request. :shipit:

## Code of Conduct

This project and everyone participating in it is governed by the following
[Code of Conduct](https://github.com/KnpLabs/snappy/blob/master/CODE_OF_CONDUCT.md).
By participating, you are expected to uphold this code.

## Reporting a bug

#### Before submitting a bug
- Verify that you are using the latest Snappy version;
- Double-check the [documentation](https://github.com/KnpLabs/snappy/blob/master/README.md)
and the [FAQ section](https://github.com/KnpLabs/snappy/blob/master/doc/faq.md)
to see if you're not misusing the library;
- Check if the issue is a Snappy issue and not a wkhtmltopdf issue (see [how to](#how-to-verify-if-the-issue-is-a-snappy-issue));
- Check if the issue has already been reported. If it has and the issue is still
open, add a comment to the existing issue instead of opening a new one.

##### How to verify if the issue is a Snappy issue
In order to verify that the issue is a Snappy issue and not a wkhtmltopdf issue,
simply copy paste the command displayed in the error message in your command prompt.
If the same error appears on the command line, then it's a wkhtmltopdf issue and
you'll have more chance to resolve your issue [there](https://github.com/wkhtmltopdf/wkhtmltopdf/issues).

#### How to submit a (good) bug report
To report a Snappy bug please open a [GitHub issue](https://github.com/KnpLabs/snappy/issues)
following the rules below.

- Use a clear and descriptive title for the issue to identify the problem;
- Describe the steps needed to reproduce the bug including a code example when
possible;
- Give as much detail as possible about your environment (OS, PHP version,
Snappy configuration, ...);

## Suggesting enhancements

To suggest Snappy enhancements please open a [GitHub issue](https://github.com/KnpLabs/snappy/issues)
following the rules below.

- Use a clear and descriptive title for the issue to identify the problem;
- Provide a step-by-step description of the suggested enhancement in as many
details as possible;
- Explain why this enhancement would be useful with one or more use cases;

## Contributing to the code

A pull request, is the best way to provide a bug fix or to propose enhancements to Snappy.

When submitting a pull request please be sure to follow the same rules described
above in [Reporting a bug](#reporting-a-bug) and [Suggesting enhancements](suggesting-enhancements)
sections depending on the nature of your change.

> Before starting to work on a large change please open an issue to ask the
maintainers if they are fine with it (no one likes to work for nothing!).

1. Fork the repository
2. Once the repository has been forked clone it locally
```
git clone git@github.com:USERNAME/snappy.git
```
3. Create a new branch
```
git checkout -b BRANCH_NAME master
```
4. Code!!!
5. Add/Update tests (if needed)
6. Update documentation (if needed)
7. Run the tests and make sure that they are passing
```
composer unit-tests
composer static-analysis
```
8. Squash your commits
9. Rebase your branch on master and fix merge conflicts
10. Open the pull request
