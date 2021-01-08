# Nextcloud Smartdev app

This is the [smartdev] app for [Nextcloud](https://nextcloud.com/).

Implements basic control over smart devices using tuya or  smart_live accounts.
 
![Screenshot](https://github.com/reekol/smartdev/blob/master/Screenshot.png?raw=true)

## Try it 
To install it change into your Nextcloud's apps directory:

    cd nextcloud/apps

Then run:

    git clone https://github.com/reekol/smartdev.git smartdev

Then install the dependencies using:

	make composer

## Frontend development

- 👩‍💻 Run `make dev-setup` to install the frontend dependencies
- 🏗 To build the Javascript whenever you make changes, run `make build-js`

To continuously run the build when editing source files you can make use of the `make watch-js` command.

## Todo list

- Api interface.
- Rename device.
- More advanced controlls.
- Schedule using nc tasks/calendar.
