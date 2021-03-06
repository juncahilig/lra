import loadable from '@loadable/component';

export const Modal = loadable(() => import('./Modal'));
export const Skeleton = loadable(() => import('./Skeleton'));
export const Snackbar = loadable(() => import('./Snackbar'));
export const Table = loadable(() => import('./Table'));
export const TablePaginationActions = loadable(() =>
    import('./TablePaginationActions'),
);
export const TableToolbar = loadable(() =>
    import('./TableToolbar/TableToolbar'),
);
