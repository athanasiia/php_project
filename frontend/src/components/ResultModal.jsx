import styles from '../styles/Modal.module.css';

function ResultModal ({show, resultData, resultMessage, onClose}) {
    if (!show) return null;

    return (
        <div className={styles.modalOverlay}>
            <div className={styles.modal}>
                <div className={styles.modalContent}>
                    <h3>{resultData ? 'Success!' : 'Error'}</h3>
                    <p>{resultMessage}</p>

                    <button
                        className={styles.modalButton}
                        onClick={onClose}
                    >
                        Return to home page
                    </button>
                </div>
            </div>
        </div>
    );
}

export default ResultModal;