import axios from "axios";

let csrfPromise: Promise<void> | null = null;

type TodoCardElement = HTMLElement & {
    dataset: DOMStringMap & {
        todoDone?: string;
        todoDeadline?: string;
    };
};

const ensureCsrfCookie = async () => {
    if (csrfPromise === null) {
        csrfPromise = axios
            .get("/sanctum/csrf-cookie")
            .then(() => {
                /* cookie prepared */
            })
            .catch((error) => {
                csrfPromise = null;
                throw error;
            });
    }

    return csrfPromise;
};

const parseDeadline = (value: string | undefined): number => {
    if (!value) return Number.POSITIVE_INFINITY;

    const isoCandidate = value.includes("T") ? value : value.replace(" ", "T");
    const timestamp = Date.parse(isoCandidate);

    return Number.isNaN(timestamp) ? Number.POSITIVE_INFINITY : timestamp;
};

const toDoneValue = (value: string | undefined): number =>
    value === "1" ? 1 : 0;

const compareCards = (
    source: TodoCardElement,
    target: TodoCardElement
): number => {
    const doneDiff =
        toDoneValue(source.dataset.todoDone) -
        toDoneValue(target.dataset.todoDone);

    if (doneDiff !== 0) return doneDiff;

    const sourceDeadline = parseDeadline(source.dataset.todoDeadline);
    const targetDeadline = parseDeadline(target.dataset.todoDeadline);

    if (sourceDeadline === targetDeadline) return 0;

    return sourceDeadline < targetDeadline ? -1 : 1;
};

const animateMove = (
    card: TodoCardElement,
    reference: TodoCardElement | null
) => {
    const parent = card.parentElement;

    if (!parent) return;

    const firstRect = card.getBoundingClientRect();

    if (reference) {
        parent.insertBefore(card, reference);
    } else {
        parent.appendChild(card);
    }

    const lastRect = card.getBoundingClientRect();
    const deltaX = firstRect.left - lastRect.left;
    const deltaY = firstRect.top - lastRect.top;

    if (deltaX === 0 && deltaY === 0) return;

    if (typeof card.animate === "function") {
        card.animate(
            [
                {
                    transform: `translate(${deltaX}px, ${deltaY}px)`,
                    opacity: 0.95,
                },
                { transform: "translate(0, 0)", opacity: 1 },
            ],
            { duration: 200, easing: "ease-out" }
        );
    }
};

const reorderTodoCard = (card: TodoCardElement) => {
    const list = card.parentElement;

    if (!list) return;

    const allCards = Array.from(
        list.querySelectorAll<TodoCardElement>("[data-todo-card]")
    );

    const siblings = allCards.filter((el) => el !== card);

    for (const sibling of siblings) {
        if (compareCards(card, sibling) < 0) {
            animateMove(card, sibling);
            return;
        }
    }

    animateMove(card, null);
};

// toggle apiを呼び出す
const toggle = async (id: number, done: boolean) => {
    await ensureCsrfCookie();

    const res = await axios.put(
        "/api/todo/toggle",
        {
            id: id,
            done: done,
        },
        {
            headers: {
                "Content-Type": "application/json; charset=utf-8",
            },
            withCredentials: true,
            withXSRFToken: true,
        }
    );
    return res.status;
};

// checkboxの状態が変わったらtoggle apiを呼び出す
const onCheckChange = async (e: Event) => {
    if (e.type !== "change") return;

    if (!(e.target instanceof HTMLInputElement)) return;

    if (!e.target.hasAttribute("todo-id")) return;

    if (!e.target.classList.contains("todo-done")) return;

    const attrId = e.target.getAttribute("todo-id") ?? "";

    if (!/^[1-9]+\d*$/.test(attrId)) {
        throw Error(`todo-id属性の値が不正です todo-id: ${attrId}`);
    }

    const todoId = Number(attrId);
    const done = e.target.checked;

    const status = await toggle(todoId, done);

    if (status < 200 || status >= 300) {
        alert("更新に失敗しました。通信状況を見てもう一度お試しください");

        // エラー時はチェックを元に戻す
        e.target.checked = !done;

        if (500 <= status) {
            console.error(
                `サーバーエラーが発生しました\nステータス: ${status}`
            );
        }

        return;
    }

    const card = e.target.closest<TodoCardElement>("[data-todo-card]");

    if (!card) return;

    card.dataset.todoDone = done ? "1" : "0";

    reorderTodoCard(card);
};

// windowのloadイベントで初期化
window.addEventListener("DOMContentLoaded", () => {
    document
        .querySelectorAll<HTMLInputElement>(".todo-done")
        .forEach((el) => {
            el.addEventListener("change", onCheckChange);
        });
});
