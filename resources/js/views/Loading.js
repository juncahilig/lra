import React from 'react';
import { withStyles, Grid, CircularProgress } from '@material-ui/core';

import darkLogo from '../../img/logos/short-dark.svg';
import lightLogo from '../../img/logos/short-light.svg';

const Loading = props => (
    <Grid
        container
        justify="center"
        alignItems="center"
        className={props.classes.container}
    >
        <Grid item>
            <Grid item className={props.classes.logoContainer}>
                <img
                    src={props.pageProps.nightMode ? darkLogo : lightLogo}
                    alt="company-logo"
                    className={props.classes.logo}
                />
            </Grid>

            <Grid item className={props.classes.loadingContainer}>
                <CircularProgress color="primary" />
            </Grid>
        </Grid>
    </Grid>
);

const styles = theme => ({
    container: {
        height: '100vh',
    },

    logoContainer: {
        padding: '0.75rem',
        textAlign: 'center',
    },

    logo: {
        width: '5rem',
        height: '5rem',
    },

    loadingContainer: {
        padding: '0.75rem',
        textAlign: 'center',
    },
});

export default withStyles(styles)(Loading);
