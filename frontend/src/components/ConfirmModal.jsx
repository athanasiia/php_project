import styles from '../styles/Modal.module.css';

function ConfirmModal ({show, confirmationMessage, action, onCancel, onConfirm, isSubmitting}) {
    if (!show) return null;

    return (
        <div className={styles.modalOverlay}>
            <div className={styles.modal}>
                <div className={styles.modalContent}>
                    <h3>Confirm Selection</h3>
                    <p>{confirmationMessage}</p>
                    <div className={styles.modalActions}>
                        <button className={styles.modalButton} onClick={onCancel}>
                            Cancel
                        </button>
                        <button className={styles.modalButton} onClick={onConfirm} disabled={isSubmitting}>
                            {action}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    );
}

export default ConfirmModal;